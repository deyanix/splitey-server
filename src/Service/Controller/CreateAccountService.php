<?php

namespace App\Service\Controller;

use App\Entity\CreateAccount;
use App\Entity\User;
use App\Model\Form\CreateAccountData;
use App\Service\RandomizerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAccountService {
	private EntityManagerInterface $entityManager;
	private MailerInterface $mailer;
	private UserPasswordHasherInterface $passwordHasher;
	private RandomizerService $randomizerService;
	private UserService $userService;

	public function __construct(
		EntityManagerInterface $entityManager,
		MailerInterface $mailer,
		UserPasswordHasherInterface $passwordHasher,
		RandomizerService $randomizerService,
		UserService $userService
	) {
		$this->entityManager = $entityManager;
		$this->mailer = $mailer;
		$this->passwordHasher = $passwordHasher;
		$this->randomizerService = $randomizerService;
		$this->userService = $userService;
	}

	private function sendMail(CreateAccount $createAccount): void {
		$email = (new TemplatedEmail())
			->from($_ENV['NOREPLY_ADDRESS'])
			->to($createAccount->getUser()->getEmail())
			->subject('Welcome to Splitey')
			->htmlTemplate('emails/create-account.html.twig')
			->textTemplate('emails/create-account.txt.twig')
			->context([
				'user' => $createAccount->getUser(),
				'url' => $_ENV['WEBAPP_URL'] . $createAccount->getToken()
			]);
		$this->mailer->send($email);
	}

	private function createToken(User $user): CreateAccount {
		$createAccount = new CreateAccount();
		$createAccount->setUser($user);
		$createAccount->setToken($this->randomizerService->getString(63));
		$createAccount->setExpirationDate((new DateTime())->modify('+12 hours'));
		return $createAccount;
	}

	public function createAccount(CreateAccountData $data): User {
		if ($this->userService->getUserByEmail($data->getEmail()) !== null)  {
			throw new BadRequestException('An account with this email address already exists');
		}

		if ($this->userService->getUserByUsername($data->getUsername()))  {
			throw new BadRequestException('An account with this username already exists');
		}

		$user = new User();
		$user->setEmail($data->getEmail());
		$user->setFirstName($data->getFirstName());
		$user->setLastName($data->getLastName());
		$user->setUsername($data->getUsername());
		$user->setPassword($this->passwordHasher->hashPassword($user, $data->getPassword()));
		$this->entityManager->persist($user);

		$createAccount = $this->createToken($user);
		$this->entityManager->persist($createAccount);

		$this->sendMail($createAccount);
		$this->entityManager->flush();
		return $user;
	}

	public function resend(User $user): void {
		$createAccount = $this->createToken($user);
		$this->entityManager->persist($createAccount);

		$this->sendMail($createAccount);
		$this->entityManager->flush();
	}
}
