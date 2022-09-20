<?php

namespace App\Service\Controller;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;

class UserService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly UserRepository $userRepository,
		private readonly Security $security,
		private readonly MailerInterface $mailer,
		private readonly EmailConfirmationService $emailConfirmationService,
	) {	}

	public function getCurrentUser(): ?User {
		$user = $this->security->getUser();
		if ($user instanceof User) {
			return $user;
		}
		return null;
	}

	public function getUserByLogin(string $login): ?User {
		return $this->userRepository->findByUsernameOrEmail($login);
	}

	public function getUserByUsername(string $username): ?User {
		return $this->userRepository->findOneBy(['username' => $username]);
	}

	public function getUserByEmail(string $email): ?User {
		return $this->userRepository->findOneBy(['email' => $email]);
	}

	private function sendConfirmationMail(EmailConfirmationToken $token, string $newEmail): void {
		$email = (new TemplatedEmail())
			->from($_ENV['NOREPLY_ADDRESS'])
			->to($newEmail)
			->subject('Confirm the email to Splitey')
			->htmlTemplate('emails/change-email.html.twig')
			->textTemplate('emails/change-email.txt.twig')
			->context([
				'user' => $token->getUser(),
				'url' => $_ENV['WEBAPP_URL'] . $token->getToken()
			]);
		$this->mailer->send($email);
	}

	public function changeEmail(string $email): void {
		if ($email === $this->getCurrentUser()->getEmail()) {
			throw new BadRequestException('User has already this address email');
		}

		if ($this->getUserByEmail($email) !== null) {
			throw new BadRequestException('An account with this email address already exists');
		}

		$token = $this->emailConfirmationService->createToken($this->getCurrentUser(), $email);
		$this->entityManager->persist($token);
		$this->sendConfirmationMail($token, $email);
		$this->entityManager->flush();
	}
}
