<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SettlementMember {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\ManyToOne(targetEntity: Settlement::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "settlement_id", referencedColumnName: 'id', nullable: true)]
	private Settlement $settlement;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: 'id', nullable: true)]
	private ?User $user;

	#[ORM\ManyToOne(targetEntity: ExternalContact::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "external_contact_id", referencedColumnName: 'id', nullable: true)]
	private ?ExternalContact $externalContact;
}
