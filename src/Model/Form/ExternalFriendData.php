<?php

namespace App\Model\Form;

use App\Entity\ExternalFriend;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class ExternalFriendData {
	public static function fromEntity(ExternalFriend $entity): ExternalFriendData {
		$data = new ExternalFriendData();
		$data->setFirstName($entity->getFirstName());
		$data->setLastName($entity->getLastName());
		return $data;
	}

	#[OA\Property(type: 'string')]
	#[Assert\Length(max: 63)]
	#[Assert\NotBlank]
	#[Assert\Type("string")]
	private mixed $firstName;

	#[OA\Property(type: 'string')]
	#[Assert\Length(max: 63)]
	#[Assert\NotBlank(groups: ["external_friend:read"])]
	#[Assert\Type("string")]
	private mixed $lastName;

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

	public function toEntity(ExternalFriend $friend = new ExternalFriend()): ExternalFriend {
		$friend->setFirstName($this->getFirstName());
		$friend->setLastName($this->getLastName());
		return $friend;
	}
}
