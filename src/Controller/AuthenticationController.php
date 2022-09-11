<?php

namespace App\Controller;

use App\Entity\Device;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Exception\FormValidationException;
use App\Form\AuthorizedResetPasswordForm;
use App\Form\CreateAccountForm;
use App\Form\ResetPasswordForm;
use App\Model\Form\AuthorizedResetPasswordData;
use App\Model\Form\CreateAccountData;
use App\Model\Form\ResetPasswordData;
use App\Model\LoginResult;
use App\Repository\DeviceRepository;
use App\Repository\RefreshTokenRepository;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use App\Service\Security\AccessTokenService;
use App\Service\Security\RefreshTokenService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Rest\Route('/auth')]
#[OA\Tag(name: 'Authentication')]
#[Nelmio\Security(name: null)]
class AuthenticationController extends AbstractController {
	private EntityManagerInterface $entityManager;
	private RefreshTokenService $refreshTokenService;
	private AccessTokenService $accessTokenService;
	private RefreshTokenRepository $refreshTokenRepository;
	private DeviceRepository $deviceRepository;
	private UserRepository $userRepository;
	private UserPasswordHasherInterface $passwordHasher;
	private FormFactoryInterface $formFactory;

	public function __construct(EntityManagerInterface      $entityManager,
								RefreshTokenService         $refreshTokenService,
	                            AccessTokenService          $accessTokenService,
	                            RefreshTokenRepository      $refreshTokenRepository,
	                            UserRepository              $userRepository,
	                            UserPasswordHasherInterface $passwordHasher,
	                            FormFactoryInterface        $formFactory) {
		$this->entityManager = $entityManager;
		$this->refreshTokenService = $refreshTokenService;
		$this->accessTokenService = $accessTokenService;
		$this->refreshTokenRepository = $refreshTokenRepository;
		$this->userRepository = $userRepository;
		$this->deviceRepository = $entityManager->getRepository(Device::class);
		$this->passwordHasher = $passwordHasher;
		$this->formFactory = $formFactory;
	}

	#[Rest\Post("/login")]
	#[Rest\RequestParam('login', description: 'Name or email of the user')]
	#[Rest\RequestParam('password', description: 'The password in plain text')]
	#[Rest\RequestParam('deviceUuid', description: 'UUID', nullable: true)]
	#[Rest\RequestParam('rememberMe', default: false)]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Get authentication tokens')]
	public function login(string $login, string $password, ?string $deviceUuid, bool $rememberMe): array {
		$user = $this->userRepository->findByUsernameOrEmail($login);
		if ($user === null) {
			throw new UnauthorizedHttpException('Wrong credentials', 'Wrong credentials');
		}

		$passwordValid = $this->passwordHasher->isPasswordValid($user, $password);
		if (!$passwordValid) {
			throw new UnauthorizedHttpException('Wrong credentials', 'Wrong credentials');
		}

		$device = $this->deviceRepository->findOneBy(['uuid' => $deviceUuid]);
		if ($device === null) {
			$device = new Device();
			if (!$rememberMe) {
				$device->setUuid(Uuid::v4());
			}
			$device->setUser($user);
		} else {
			$this->refreshTokenRepository->invalidateTokensByDevice($device);
		}

		$accessToken = $this->accessTokenService->createToken($user->getUsername());
		$refreshToken = $this->refreshTokenService->createToken($device, $rememberMe);
		return [
			'accessToken' => $accessToken->toString(),
			'refreshToken' => $refreshToken->getToken(),
			'refreshTokenExpiration' => $refreshToken->getExpirationDate(),
			'deviceUuid' => $device->getUuid()->toRfc4122()
		];
	}

	#[Rest\Post('/refresh', name: 'refresh')]
	#[Rest\RequestParam('refreshToken')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(
		summary: 'Refresh authentication tokens'
	)]
	public function refresh(string $refreshToken) {
		$token = $this->refreshTokenRepository->findOneBy(['token' => $refreshToken]);
		try {
			$this->refreshTokenService->validateToken($token);
		} catch (Exception) {
			$this->refreshTokenRepository->invalidateTokensByDevice($token->getDevice());
			throw new UnauthorizedHttpException('Invalid token provided', 'Invalid token provided');
		}

		if ($token->getRefreshDate() >= new DateTime()) {
			$this->refreshTokenRepository->invalidateTokensByDevice($token->getDevice());
			$refreshToken = $this->refreshTokenService->createToken($token->getDevice());
		} else {
			$refreshToken = $token;
		}

		$accessToken = $this->accessTokenService->createToken($token->getDevice()->getUser()->getUsername());
		$loginResult = new LoginResult();
		$loginResult->setAccessToken($accessToken->toString());
		$loginResult->setRefreshToken($refreshToken->getToken());
		$loginResult->setRefreshTokenExpirationDate($refreshToken->getExpirationDate());
		return $loginResult;
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/invalidate', name: 'invalidate')]
	#[Rest\RequestParam('refreshToken')]
	#[OA\Post(summary: 'Invalid refresh tokens')]
	public function invalidate(string $refreshToken) {
		$token = $this->refreshTokenRepository->findOneBy(['token' => $refreshToken]);
		if (!$token) {
			throw new UnauthorizedHttpException('Invalid refresh token.', 'Invalid refresh token.');
		}
		try {
			$this->refreshTokenService->validateToken($token);
		} catch (Exception) {
			throw new UnauthorizedHttpException('Invalid refresh token.', 'Invalid refresh token.');
		}
		$this->refreshTokenRepository->invalidateTokensByDevice($token->getDevice());
		return ['result' => true];
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/create-account', name: 'create_account')]
	#[OA\Post(summary: 'Create an account', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'firstName',
			type: 'string'
		),
		new OA\Property(
			property: 'lastName',
			type: 'string'
		),
		new OA\Property(
			property: 'username',
			type: 'string'
		),
		new OA\Property(
			property: 'email',
			type: 'string'
		),
		new OA\Property(
			property: 'password',
			type: 'string'
		),
		new OA\Property(
			property: 'captcha',
			type: 'string'
		)],
	)))]
	public function createAccount(Request $request) {
		$form = $this->formFactory->create(CreateAccountForm::class);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}

		if ($this->userRepository->findOneBy(['email' => $form->get('email')->getData()]) !== null)  {
			$form->get('email')->addError(new FormError('An account with this email address already exists'));
			throw new FormValidationException($form);
		}

		if ($this->userRepository->findOneBy(['username' => $form->get('username')->getData()]) !== null)  {
			$form->get('email')->addError(new FormError('An account with this username already exists'));
			throw new FormValidationException($form);
		}

		/** @var CreateAccountData $createAccount */
		$createAccount = $form->getData();

		$user = new User();
		$user->setEmail($createAccount->getEmail());
		$user->setFirstName($createAccount->getFirstName());
		$user->setLastName($createAccount->getLastName());
		$user->setUsername($createAccount->getUsername());
		$user->setPassword($this->passwordHasher->hashPassword($user, $createAccount->getPassword()));
		$this->entityManager->persist($user);
		$this->entityManager->flush();

		return [
			'data' => $user->getId()
		];
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/reset-password', name: 'reset_password')]
	#[OA\Post(summary: 'Send mail with instruction', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'email',
			type: 'string'
		)],
	)))]
	public function resetPassword(Request $request, MailerInterface $mailer) {
		$form = $this->formFactory->create(ResetPasswordForm::class);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}

		/** @var ResetPasswordData $resetPasswordData */
		$resetPasswordData = $form->getData();
		$user = $this->userRepository->findOneBy(['email' => $resetPasswordData->getEmail()]);
		if (!($user instanceof User)) {
			return ['data' => true];
		}

		$token = strtr(base64_encode(random_bytes(63)), '+/', '-_');
		$resetPassword = new ResetPassword();
		$resetPassword->setUser($user);
		$resetPassword->setToken($token);
		$resetPassword->setExpirationDate((new DateTime())->modify('+1 hour'));
		$this->entityManager->persist($resetPassword);
		$this->entityManager->flush();

		$email = (new TemplatedEmail())
			->from($_ENV['NOREPLY_ADDRESS'])
			->to($resetPasswordData->getEmail())
			->subject('Reset password to Splitey')
			->text('Hello! Test')
			->htmlTemplate('emails/reset-password.html.twig')
			->textTemplate('emails/reset-password.txt.twig')
			->context([
				'user' => $user,
				'url' => $_ENV['WEBAPP_URL'] . $token
			]);
		$mailer->send($email);
		return ['data' => true];
	}

	#[Rest\View(statusCode: 200)]
	#[Rest\Post('/authorized-reset-password', name: 'authorized_reset_password')]
	#[OA\Post(summary: 'Reset password via token', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'token',
			type: 'string'
		),
		new OA\Property(
			property: 'password',
			type: 'string'
		)],
	)))]
	public function authorizedResetPassword(Request $request, ResetPasswordRepository $resetPasswordRepository) {
		$form = $this->formFactory->create(AuthorizedResetPasswordForm::class);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}

		/** @var AuthorizedResetPasswordData $data */
		$data = $form->getData();
		$resetPassword = $resetPasswordRepository->findOneBy(['token' => $data->getToken()]);
		if (!($resetPassword instanceof ResetPassword)  || $resetPassword->getUsageDate() !== null || $resetPassword->getExpirationDate() < new DateTime()) {
			return ['data' => false];
		}

		$resetPassword->setUsageDate(new DateTime());
		$this->entityManager->persist($resetPassword);

		$user = $resetPassword->getUser();
		$user->setPassword($this->passwordHasher->hashPassword($user, $data->getPassword()));
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		return ['data' => true];
	}

}
