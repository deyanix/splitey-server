<?php

namespace App\Service\Controller;

use App\Entity\ResetPasswordToken;
use App\Entity\User;
use App\Model\Form\AuthorizedResetPasswordData;
use App\Model\Form\ResetPasswordData;
use App\Repository\ResetPasswordTokenRepository;
use App\Repository\UserRepository;
use App\Service\RandomizerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordService {
	private EntityManagerInterface $entityManager;
	private UserRepository $userRepository;
	private ResetPasswordTokenRepository $resetPasswordRepository;
	private MailerInterface $mailer;
	private UserPasswordHasherInterface $passwordHasher;
	private RandomizerService $randomizerService;

	public function __construct(
		EntityManagerInterface $entityManager,
		MailerInterface $mailer,
		UserPasswordHasherInterface $passwordHasher,
		RandomizerService $randomizerService
	) {
		$this->entityManager = $entityManager;
		$this->userRepository = $entityManager->getRepository(User::class);
		$this->resetPasswordRepository = $entityManager->getRepository(ResetPasswordToken::class);
		$this->mailer = $mailer;
		$this->passwordHasher = $passwordHasher;
		$this->randomizerService = $randomizerService;
	}

	private function sendResetPasswordMail(ResetPasswordToken $token): void {
		$email = (new TemplatedEmail())
			->from($_ENV['NOREPLY_ADDRESS'])
			->to($token->getUser()->getEmail())
			->subject('Reset password to Splitey')
			->htmlTemplate('emails/reset-password.html.twig')
			->textTemplate('emails/reset-password.txt.twig')
			->context([
				'user' => $token->getUser(),
				'url' => $_ENV['WEBAPP_URL'] . $token->getToken()
			]);
		$this->mailer->send($email);
	}

	public function resetPassword(ResetPasswordData $data): void {
		$user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
		if (!($user instanceof User)) {
			return;
		}

		$resetPassword = new ResetPasswordToken();
		$resetPassword->setUser($user);
		$resetPassword->setToken($this->randomizerService->getString(64));
		$resetPassword->setExpirationDate((new DateTime())->modify('+1 hour'));
		$this->entityManager->persist($resetPassword);
		$this->entityManager->flush();
	}

	public function authorizedResetPassword(AuthorizedResetPasswordData $data): bool {
		$resetPassword = $this->resetPasswordRepository->findOneBy(['token' => $data->getToken()]);
		if (!($resetPassword instanceof ResetPasswordToken) || $resetPassword->getUsageDate() !== null || $resetPassword->getExpirationDate() < new DateTime()) {
			return false;
		}

		$resetPassword->setUsageDate(new DateTime());
		$this->entityManager->persist($resetPassword);

		$user = $resetPassword->getUser();
		$user->setPassword($this->passwordHasher->hashPassword($user, $data->getPassword()));
		$this->entityManager->persist($user);
		$this->entityManager->flush();
		return true;
	}
}
