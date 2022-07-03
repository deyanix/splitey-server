<?php

namespace App\Controller;

use App\Entity\Device;
use App\Model\LoginResult;
use App\Repository\RefreshTokenRepository;
use App\Repository\UserRepository;
use App\Service\Security\AccessTokenService;
use App\Service\Security\RefreshTokenService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Nelmio\ApiDocBundle\Model\Model;
use OpenApi\Attributes as OA;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\DateTime;
use Nelmio\ApiDocBundle\Annotation as Nelmio;

#[Rest\Route('/auth')]
#[OA\Tag(name: 'Authentication')]
class AuthenticationController extends AbstractController {
	private EntityManagerInterface $entityManager;
	private RefreshTokenService $refreshTokenService;
	private AccessTokenService $accessTokenService;
	private RefreshTokenRepository $refreshTokenRepository;
	private EntityRepository $deviceRepository;
	private UserRepository $userRepository;
	private UserPasswordHasherInterface $passwordHasher;

	public function __construct(EntityManagerInterface      $entityManager,
								RefreshTokenService         $refreshTokenService,
	                            AccessTokenService          $accessTokenService,
	                            RefreshTokenRepository      $refreshTokenRepository,
	                            UserRepository              $userRepository,
	                            UserPasswordHasherInterface $passwordHasher) {
		$this->entityManager = $entityManager;
		$this->refreshTokenService = $refreshTokenService;
		$this->accessTokenService = $accessTokenService;
		$this->refreshTokenRepository = $refreshTokenRepository;
		$this->userRepository = $userRepository;
		$this->deviceRepository = $entityManager->getRepository(Device::class);
		$this->passwordHasher = $passwordHasher;
	}

	#[Rest\Post("/login")]
	#[Rest\View(statusCode: 200)]
	#[Rest\RequestParam('login', description: 'Name or email of the user')]
	#[Rest\RequestParam('password', description: 'The password in plain text')]
	#[Rest\RequestParam('deviceUuid', description: 'UUID', nullable: true)]
	#[Rest\RequestParam('rememberMe', default: false)]
	#[OA\Post(summary: 'Get a authentication token')]
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

	#[OA\Response(
		response: 200,
		description: 't',
		content: new OA\JsonContent(type: 'array', items: new OA\Items(type: 'string'))
	)]
	#[Rest\Post('/refresh', name: 'refresh')]
	#[Rest\RequestParam('refreshToken')]
	#[Rest\View(statusCode: 200)]
	public function refresh(string $refreshToken) {
		$token = $this->refreshTokenRepository->findOneBy(['token' => $refreshToken]);
		try {
			$this->refreshTokenService->validateToken($token);
		} catch (Exception) {
			$this->refreshTokenRepository->invalidateTokensByPrevious($token);
			throw new UnauthorizedHttpException('Invalid token provided', 'Invalid token provided');
		}

		if ($token->getRefreshDate() >= new DateTime()) {
			$this->refreshTokenRepository->invalidateTokensByPrevious($token);
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
}
