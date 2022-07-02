<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Transfer {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'string', length: 63)]
	private string $name;

	#[ORM\ManyToOne(targetEntity: SettlementMember::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "paying_member_id", referencedColumnName: 'id', nullable: true)]
	private SettlementMember $payingMember;
}
