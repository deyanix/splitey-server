<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity]
class TransferDivision {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\ManyToOne(targetEntity: Transfer::class, cascade: ['persist'], inversedBy: 'divisions')]
	#[ORM\JoinColumn(name: "transfer_id", referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
	#[Serializer\Groups(["transfer:read"])]
	private Transfer $transfer;

	#[ORM\ManyToOne(targetEntity: SettlementMember::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "member_id", referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
	#[Serializer\Groups(["transfer:read"])]
	private SettlementMember $member;

	#[ORM\Column(type: 'float')]
	#[Serializer\Groups(["transfer:read"])]
	private float $amount;

	public function getId(): int {
		return $this->id;
	}

	public function getTransfer(): Transfer {
		return $this->transfer;
	}

	public function setTransfer(Transfer $transfer): void {
		$this->transfer = $transfer;
	}

	public function getMember(): SettlementMember {
		return $this->member;
	}

	public function setMember(SettlementMember $member): void {
		$this->member = $member;
	}

	public function getAmount(): float {
		return $this->amount;
	}

	public function setAmount(float $amount): void {
		$this->amount = $amount;
	}
}
