<?php

namespace App\Entity;

use App\Repository\CreateAccountRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CreateAccountRepository::class)]
class CreateAccount {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'string', length: 63, unique: true, options: ['fixed' => true])]
	private string $token;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
	private User $user;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?DateTime $usageDate;

	#[ORM\Column(type: 'datetime')]
	private DateTime $expirationDate;

	public function getId(): int {
		return $this->id;
	}

	public function getToken(): string {
		return $this->token;
	}

	public function setToken(string $token): void {
		$this->token = $token;
	}

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): void {
		$this->user = $user;
	}

	public function getUsageDate(): ?DateTime {
		return $this->usageDate;
	}

	public function setUsageDate(?DateTime $usageDate): void {
		$this->usageDate = $usageDate;
	}

	public function getExpirationDate(): DateTime {
		return $this->expirationDate;
	}

	public function setExpirationDate(DateTime $expirationDate): void {
		$this->expirationDate = $expirationDate;
	}
}
