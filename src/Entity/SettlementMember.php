<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity]
class SettlementMember {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[Serializer\Groups(["settlement_member:read"])]
	private int $id;

	#[ORM\ManyToOne(targetEntity: Settlement::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "settlement_id", referencedColumnName: 'id', nullable: true)]
	private Settlement $settlement;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: 'id', nullable: true)]
	private ?User $user = null;

	#[ORM\ManyToOne(targetEntity: ExternalContact::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "external_contact_id", referencedColumnName: 'id', nullable: true)]
	private ?ExternalContact $externalContact = null;

	public function getId(): int {
		return $this->id;
	}

	public function getSettlement(): Settlement {
		return $this->settlement;
	}

	public function setSettlement(Settlement $settlement): void {
		$this->settlement = $settlement;
	}

	public function getUser(): ?User {
		return $this->user;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getUserId(): ?int {
		return $this->getUser()?->getId();
	}

	public function setUser(?User $user): void {
		$this->user = $user;
	}

	public function getExternalContact(): ?ExternalContact {
		return $this->externalContact;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getExternalContactId(): ?int {
		return $this->getExternalContact()?->getId();
	}

	public function setExternalContact(?ExternalContact $externalContact): void {
		$this->externalContact = $externalContact;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getFirstName(): ?string {
		return $this->getUser()?->getFirstName() ?? $this->getExternalContact()?->getFirstName();
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getLastName(): ?string {
		return $this->getUser()?->getLastName() ?? $this->getExternalContact()?->getLastName();
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getType(): ?string {
		if ($this->getUser()) {
			return 'user';
		}
		if ($this->getExternalContact()) {
			return 'external_contact';
		}
		return null;
	}
}
