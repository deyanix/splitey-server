<?php

namespace App\Model\Form;

use App\Validator as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData {
	#[Assert\Length(max: 63)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $email;

	#[AppAssert\ReCaptcha]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $captcha;

	public function getEmail(): mixed {
		return $this->email;
	}

	public function setEmail(mixed $email): void {
		$this->email = $email;
	}

	public function getCaptcha(): mixed {
		return $this->captcha;
	}

	public function setCaptcha(mixed $captcha): void {
		$this->captcha = $captcha;
	}
}
