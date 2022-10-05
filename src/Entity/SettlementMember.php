<?php

namespace App\Entity;

use App\Repository\SettlementMemberRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: SettlementMemberRepository::class)]
class SettlementMember {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[Serializer\Groups(["settlement_member:read"])]
	private int $id;

	#[ORM\Column(type: 'boolean')]
	private bool $active;

	#[ORM\ManyToOne(targetEntity: Settlement::class, cascade: ['persist'], inversedBy: 'members')]
	#[ORM\JoinColumn(name: "settlement_id", referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
	private Settlement $settlement;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "user_id", referencedColumnName: 'id', nullable: true)]
	private ?User $user = null;

	#[ORM\ManyToOne(targetEntity: ExternalFriend::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "external_friend_id", referencedColumnName: 'id', nullable: true)]
	private ?ExternalFriend $externalFriend = null;

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

	public function getExternalFriend(): ?ExternalFriend {
		return $this->externalFriend;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getExternalContactId(): ?int {
		return $this->getExternalFriend()?->getId();
	}

	public function setExternalFriend(?ExternalFriend $externalFriend): void {
		$this->externalFriend = $externalFriend;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getFirstName(): ?string {
		return $this->getUser()?->getFirstName() ?? $this->getExternalFriend()?->getFirstName();
	}

	#[Serializer\VirtualProperty]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getLastName(): ?string {
		return $this->getUser()?->getLastName() ?? $this->getExternalFriend()?->getLastName();
	}

	public function getType(): ?SettlementMemberType {
		if ($this->getUser()) {
			return SettlementMemberType::USER;
		}
		if ($this->getExternalFriend()) {
			return SettlementMemberType::EXTERNAL_FRIEND;
		}
		return null;
	}

	#[Serializer\VirtualProperty]
	#[Serializer\SerializedName("type")]
	#[Serializer\Groups(["settlement_member:read"])]
	public function getStringType(): ?string {
		return $this->getType()?->name;
	}

	public function isActive(): bool {
		return $this->active;
	}

	public function setActive(bool $active): void {
		$this->active = $active;
	}
}
