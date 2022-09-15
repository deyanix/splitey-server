<?php

namespace App\Service\Security;

use App\Entity\Device;
use App\Entity\RefreshToken;
use App\Service\RandomizerService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;

class RefreshTokenService {
	private EntityManagerInterface $entityManager;
	private LoggerInterface $logger;
	private RandomizerService $randomizerService;

	public function __construct(EntityManagerInterface $entityManager,
								LoggerInterface        $logger,
								RandomizerService      $randomizerService) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->randomizerService = $randomizerService;
	}

	/**
	 * Creates the refresh token and inserts it into database.
	 *
	 * @param int  $userId The user whom the token is issued for.
	 * @param bool $rememberMe If the 'remember me' option is enabled. If <code>true</code>, the token has 30 days of
	 *     validity, 12 hours otherwise.
	 *
	 * @return RefreshToken The created token persisted in database.
	 */
	public function createToken(Device $device, bool $rememberMe = false): RefreshToken {
		$randomString = $this->createRandomString();
		$refreshDate = (new DateTime())->modify($this->getDateTimeModifier(false));
		$expirationDate = (new DateTime())->modify($this->getDateTimeModifier($rememberMe));

		$refreshToken = new RefreshToken();
		$refreshToken->setDevice($device);
		$refreshToken->setToken($randomString);
		$refreshToken->setRefreshDate($refreshDate);
		$refreshToken->setExpirationDate($expirationDate);
		$refreshToken->setRememberMe($rememberMe);

		$this->entityManager->persist($refreshToken);
		$this->entityManager->flush();
		return $refreshToken;
	}

	/**
	 * Validates the provided token and throws matching exceptions.
	 *
	 * @param RefreshToken|null $refreshToken The token to be validated.
	 *
	 * @throws Exception
	 */
	public function validateToken(?RefreshToken $refreshToken): void {
		if ($refreshToken === null) {
			throw new Exception('Missing API token.', 401);
		}
		if ($refreshToken->getInvalidationDate() !== null) {
			throw new Exception('Token already used.', 401);
		}
		if ($refreshToken->getExpirationDate() < new DateTime()) {
			throw new Exception('Token no longer valid.', 401);
		}
	}

	/**
	 * Created the modifier to apply to validity of the token.
	 *
	 * @param bool $rememberMe If the 'remember me' flag was provided.
	 *
	 * @return string <code>'+30 days'</code> if $rememberMe is <code>true</code> and <code>'+12 hours'</code>
	 *     otherwise.
	 */
	private function getDateTimeModifier(bool $rememberMe): string {
		return $rememberMe ? '+30 days' : '+12 hours';
	}

	private function createRandomString(): string {
		try {
			return bin2hex($this->randomizerService->getBytes(32));
		} catch (Exception) {
			$this->logger->warning('Unable to created random binary bytes.');
		}
		return $this->randomizerService->getString(64);
	}
}
