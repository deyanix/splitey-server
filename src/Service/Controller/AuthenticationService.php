<?php

namespace App\Service\Controller;

use App\Entity\Device;
use App\Entity\RefreshToken;
use App\Entity\User;
use App\Model\LoginResult;
use App\Repository\DeviceRepository;
use App\Repository\RefreshTokenRepository;
use App\Service\Security\AccessTokenService;
use App\Service\Security\RefreshTokenService;
use DateTime;
use Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Uid\Uuid;

class AuthenticationService {
	public function __construct(
		private readonly DeviceRepository            $deviceRepository,
		private readonly RefreshTokenRepository      $refreshTokenRepository,
		private readonly AccessTokenService          $accessTokenService,
		private readonly RefreshTokenService         $refreshTokenService,
		private readonly UserPasswordHasherInterface $passwordHasher,
		private readonly UserService                 $userService,
	) {
	}

	public function getRefreshToken(string $token): ?RefreshToken {
		$refreshToken = $this->refreshTokenRepository->findOneBy(['token' => $token]);
		try {
			$this->refreshTokenService->validateToken($refreshToken);
			return $refreshToken;
		} catch (Exception) {
			$this->refreshTokenRepository->invalidateTokensByDevice($refreshToken->getDevice());
			throw new UnauthorizedHttpException('Invalid token provided', 'Invalid token provided');
		}
	}

	public function authenticate(string $login, string $password): User {
		$user = $this->userService->getUserByLogin($login);
		if ($user === null) {
			throw new AuthenticationException('Wrong credentials');
		}
		if (!$user->isActivated()) {
			throw new AuthenticationException('Not activated user');
		}
		if ($user->isDisabled()) {
			throw new AuthenticationException('Disabled user');
		}

		$passwordValid = $this->passwordHasher->isPasswordValid($user, $password);
		if (!$passwordValid) {
			throw new AuthenticationException('Wrong credentials');
		}
		return $user;
	}

	public function login(User $user, ?string $deviceUuid, bool $rememberMe): LoginResult {
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

		$accessToken = $this->accessTokenService->createToken($user);
		$refreshToken = $this->refreshTokenService->createToken($device, $rememberMe);

		$loginResult = new LoginResult();
		$loginResult->setAccessToken($accessToken->toString());
		$loginResult->setRefreshToken($refreshToken->getToken());
		$loginResult->setRefreshTokenExpirationDate($refreshToken->getExpirationDate());
		$loginResult->setDeviceUuid($device->getUuid()->toRfc4122());
		return $loginResult;
	}

	public function refresh(RefreshToken $token): LoginResult {
		if ($token->getRefreshDate() <= new DateTime()) {
			$this->refreshTokenRepository->invalidateTokensByDevice($token->getDevice());
			$refreshToken = $this->refreshTokenService->createToken($token->getDevice());
		} else {
			$refreshToken = $token;
		}

		$accessToken = $this->accessTokenService->createToken($token->getDevice()->getUser());
		$loginResult = new LoginResult();
		$loginResult->setAccessToken($accessToken->toString());
		$loginResult->setRefreshToken($refreshToken->getToken());
		$loginResult->setRefreshTokenExpirationDate($refreshToken->getExpirationDate());
		$loginResult->setDeviceUuid($token->getDevice()->getUuid());
		return $loginResult;
	}

	public function invalidate(RefreshToken $token): void {
		$this->refreshTokenRepository->invalidateTokensByDevice($token->getDevice());
	}
}
