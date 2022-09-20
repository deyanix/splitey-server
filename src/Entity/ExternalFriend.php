<?php

namespace App\Entity;

use App\Repository\ExternalFriendRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: ExternalFriendRepository::class)]
class ExternalFriend {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[Serializer\Groups(["external_friend:read"])]
	private int $id;

	#[ORM\Column(type: 'string', length: 31)]
	#[Serializer\Groups(["external_friend:read"])]
	private string $firstName;

	#[ORM\Column(type: 'string', length: 63)]
	#[Serializer\Groups(["external_friend:read"])]
	private string $lastName;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "owner_id", referencedColumnName: 'id', nullable: true)]
	private User $owner;

	public function getId(): int {
		return $this->id;
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

	public function getOwner(): User {
		return $this->owner;
	}

	public function setOwner(User $owner): void {
		$this->owner = $owner;
	}
}
