<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity]
class FriendInvitation {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[Serializer\Groups(["friend_invitation:read"])]
	private int $id;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "sender_id", referencedColumnName: 'id', nullable: true)]
	#[Serializer\Groups(["friend_invitation:read"])]
	private User $sender;

	#[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "recipient_id", referencedColumnName: 'id', nullable: true)]
	private User $recipient;

	#[ORM\Column(type: 'datetime')]
	#[Serializer\Groups(["friend_invitation:read"])]
	private DateTime $date;

	#[ORM\Column(type: 'smallint', enumType: FriendInvitationStatus::class, options: ['unsigned' => true])]
	#[Serializer\Type("Enum")]
	#[Serializer\Groups(["friend_invitation:read"])]
	private FriendInvitationStatus $status = FriendInvitationStatus::PENDING;

	public function getId(): int {
		return $this->id;
	}

	public function setId(int $id): void {
		$this->id = $id;
	}

	public function getSender(): User {
		return $this->sender;
	}

	public function setSender(User $sender): void {
		$this->sender = $sender;
	}

	public function getRecipient(): User {
		return $this->recipient;
	}

	public function setRecipient(User $recipient): void {
		$this->recipient = $recipient;
	}

	public function getDate(): DateTime {
		return $this->date;
	}

	public function setDate(DateTime $date): void {
		$this->date = $date;
	}

	public function getStatus(): FriendInvitationStatus {
		return $this->status;
	}

	public function setStatus(FriendInvitationStatus $status): void {
		$this->status = $status;
	}
}
