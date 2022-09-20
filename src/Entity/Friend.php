<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Friend {
	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "user1_id", referencedColumnName: 'id', nullable: true)]
	private User $user1;

	#[ORM\Id]
	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "user2_id", referencedColumnName: 'id', nullable: true)]
	private User $user2;

	public function getUser1(): User {
		return $this->user1;
	}

	public function setUser1(User $user1): void {
		$this->user1 = $user1;
	}

	public function getUser2(): User {
		return $this->user2;
	}

	public function setUser2(User $user2): void {
		$this->user2 = $user2;
	}
}
