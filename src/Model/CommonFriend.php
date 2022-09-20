<?php

namespace App\Model;

class CommonFriend {
	private ?int $userId;
	private ?int $externalFriendId;
	private string $firstName;
	private string $lastName;
	private ?string $username;

	public function getUserId(): ?int {
		return $this->userId;
	}

	public function setUserId(?int $userId): void {
		$this->userId = $userId;
	}

	public function getExternalFriendId(): ?int {
		return $this->externalFriendId;
	}

	public function setExternalFriendId(?int $externalFriendId): void {
		$this->externalFriendId = $externalFriendId;
	}

	public function getFirstName(): string {
		return $this->firstName;
	}

	public function setFirstName(string $firstName): void {
		$this->firstName = $firstName;
	}

	public function getLastName(): string {
		return $this->lastName;
	}

	public function setLastName(string $lastName): void {
		$this->lastName = $lastName;
	}

	public function getUsername(): ?string {
		return $this->username;
	}

	public function setUsername(?string $username): void {
		$this->username = $username;
	}
}
