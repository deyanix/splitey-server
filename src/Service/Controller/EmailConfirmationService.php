<?php

namespace App\Service\Controller;

use App\Entity\EmailConfirmationToken;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Repository\EmailConfirmationTokenRepository;
use App\Service\RandomizerService;
use DateTime;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class EmailConfirmationService {
	public function __construct(
		private readonly RandomizerService $randomizerService,
		private readonly EmailConfirmationTokenRepository $confirmationTokenRepository,
	) {}

	public function createToken(User $user, ?string $newEmail = null): EmailConfirmationToken {
		$token = new EmailConfirmationToken();
		$token->setUser($user);
		$token->setToken($this->randomizerService->getString(63));
		$token->setExpirationDate((new DateTime())->modify('+4 hours'));
		$token->setNewEmail($newEmail);
		return $token;
	}

	public function getToken(string $token): EmailConfirmationToken {
		$resetPassword = $this->confirmationTokenRepository->findOneBy(['token' => $token]);
		if (!($resetPassword instanceof EmailConfirmationToken)) {
			throw new EntityNotFoundException('Not found a email confirmation token');
		}
		return $resetPassword;
	}

	public function validateToken(EmailConfirmationToken $token): void {
		if ($token->getUsageDate() !== null || $token->getExpirationDate() < new DateTime()) {
			throw new BadRequestException('Token has used or it is expired');
		}
	}

	public function useToken(EmailConfirmationToken $token): void {
		$token->setUsageDate(new DateTime());
	}
}
