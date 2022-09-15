<?php

namespace App\Service\Controller;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Model\Form\CreateAccountData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountService {
	public function __construct(
		private readonly EntityManagerInterface      $entityManager,
		private readonly MailerInterface             $mailer,
		private readonly UserPasswordHasherInterface $passwordHasher,
		private readonly UserService                 $userService,
		private readonly EmailConfirmationService    $emailConfirmationService,
	) {}

	private function sendActivationMail(EmailConfirmationToken $token): void {
		$email = (new TemplatedEmail())
			->from($_ENV['NOREPLY_ADDRESS'])
			->to($token->getUser()->getEmail())
			->subject('Welcome to Splitey')
			->htmlTemplate('emails/create-account.html.twig')
			->textTemplate('emails/create-account.txt.twig')
			->context([
				'user' => $token->getUser(),
				'url' => $_ENV['WEBAPP_URL'] . $token->getToken()
			]);
		$this->mailer->send($email);
	}

	public function createAccount(CreateAccountData $data): User {
		if ($this->userService->getUserByEmail($data->getEmail()) !== null) {
			throw new BadRequestException('An account with this email address already exists');
		}

		if ($this->userService->getUserByUsername($data->getUsername())) {
			throw new BadRequestException('An account with this username already exists');
		}

		$user = new User();
		$user->setEmail($data->getEmail());
		$user->setFirstName($data->getFirstName());
		$user->setLastName($data->getLastName());
		$user->setUsername($data->getUsername());
		$user->setPassword($this->passwordHasher->hashPassword($user, $data->getPassword()));
		$this->entityManager->persist($user);

		$token = $this->emailConfirmationService->createToken($user);
		$this->entityManager->persist($token);
		$this->sendActivationMail($token);
		$this->entityManager->flush();
		return $user;
	}

	public function resendActivation(User $user): void {
		if ($user->isActivated()) {
			throw new BadRequestException('User already is activated');
		}

		$token = $this->emailConfirmationService->createToken($user);
		$this->entityManager->persist($token);
		$this->sendActivationMail($token);
		$this->entityManager->flush();
	}

	public function activate(string $token): void {
		$token = $this->emailConfirmationService->getToken($token);
		$this->emailConfirmationService->validateToken($token);
		if (!$token->isAccountActivation()) {
			throw new BadRequestException('Given token doesn\'t support activation account');
		}

		$this->emailConfirmationService->useToken($token);
		$this->entityManager->persist($token);

		$user = $token->getUser();
		$user->setActivated(true);
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}

	public function confirmEmail(string $token): void {
		$token = $this->emailConfirmationService->getToken($token);
		$this->emailConfirmationService->validateToken($token);
		if ($token->isAccountActivation()) {
			throw new BadRequestException('Given token doesn\'t support email confirmation');
		}

		$this->emailConfirmationService->useToken($token);
		$this->entityManager->persist($token);

		$user = $token->getUser();
		$user->setEmail($token->getNewEmail());
		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}
