<?php

namespace App\Model\Form;

use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class AccountCreateData {
	#[Assert\Length(max: 63)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $firstName;

	#[Assert\Length(max: 63)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $lastName;

	#[Assert\Length(min: 3, max: 31)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $username;

	#[Assert\Length(max: 63)]
	#[Assert\Email]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $email;

	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $password;

	#[AppAssert\ReCaptcha]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $captcha;

	public function getFirstName(): mixed {
		return $this->firstName;
	}

	public function setFirstName(mixed $firstName): void {
		$this->firstName = $firstName;
	}

	public function getLastName(): mixed {
		return $this->lastName;
	}

	public function setLastName(mixed $lastName): void {
		$this->lastName = $lastName;
	}

	public function getUsername(): mixed {
		return $this->username;
	}

	public function setUsername(mixed $username): void {
		$this->username = $username;
	}

	public function getEmail(): mixed {
		return $this->email;
	}

	public function setEmail(mixed $email): void {
		$this->email = $email;
	}

	public function getPassword(): mixed {
		return $this->password;
	}

	public function setPassword(mixed $password): void {
		$this->password = $password;
	}

	public function getCaptcha(): mixed {
		return $this->captcha;
	}

	public function setCaptcha(mixed $captcha): void {
		$this->captcha = $captcha;
	}
}
