<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;

class Individual {
	private int $id;
	private string $firstName;
	private string $lastName;
	private ?string $username;
	#[Serializer\Type("Enum")]
	private IndividualType $type;

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id): void {
		$this->id = $id;
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

	public function getType(): IndividualType {
		return $this->type;
	}

	public function setType(IndividualType $type): void {
		$this->type = $type;
	}
}
