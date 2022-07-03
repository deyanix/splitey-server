<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class Device {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'uuid', unique: true, nullable: true)]
	private $uuid;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
	private User $user;

	public function getId(): int {
		return $this->id;
	}

	public function getUuid() {
		return $this->uuid;
	}

	public function setUuid($uuid): void {
		$this->uuid = $uuid;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): void {
		$this->user = $user;
	}
}
