<?php

namespace App\Model\Form;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorizedResetPasswordData {
	#[Assert\Length(max: 84)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $token;

	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $password;

	public function getToken(): mixed {
		return $this->token;
	}

	public function setToken(mixed $token): void {
		$this->token = $token;
	}

	public function getPassword(): mixed {
		return $this->password;
	}

	public function setPassword(mixed $password): void {
		$this->password = $password;
	}
}
