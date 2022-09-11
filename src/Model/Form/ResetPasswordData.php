<?php

namespace App\Model\Form;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordData {
	#[Assert\Length(max: 63)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $email;

	public function getEmail(): mixed {
		return $this->email;
	}

	public function setEmail(mixed $email): void {
		$this->email = $email;
	}
}
