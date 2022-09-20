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
}
