<?php

namespace App\Model;

use DateTimeInterface;
use OpenApi\Attributes as OA;

#[OA\Schema]
class LoginResult {
	private string $accessToken;
	private string $refreshToken;
	private DateTimeInterface $refreshTokenExpirationDate;
	private string $deviceUuid;

	public function getAccessToken(): string {
		return $this->accessToken;
	}

	public function setAccessToken(string $accessToken): void {
		$this->accessToken = $accessToken;
	}

	public function getRefreshToken(): string {
		return $this->refreshToken;
	}

	public function setRefreshToken(string $refreshToken): void {
		$this->refreshToken = $refreshToken;
	}

	public function getRefreshTokenExpirationDate(): DateTimeInterface {
		return $this->refreshTokenExpirationDate;
	}

	public function setRefreshTokenExpirationDate(DateTimeInterface $refreshTokenExpirationDate): void {
		$this->refreshTokenExpirationDate = $refreshTokenExpirationDate;
	}

	public function getDeviceUuid(): string {
		return $this->deviceUuid;
	}

	public function setDeviceUuid(string $deviceUuid): void {
		$this->deviceUuid = $deviceUuid;
	}
}
