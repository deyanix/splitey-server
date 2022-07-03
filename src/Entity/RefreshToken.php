<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
class RefreshToken {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'string', length: 64)]
	private string $token;

	#[ORM\ManyToOne(targetEntity: Device::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'device_id', referencedColumnName: 'id', nullable: true)]
	private Device $device;

	#[ORM\Column(type: 'datetime')]
	private DateTime $refreshDate;

	#[ORM\Column(type: 'datetime')]
	private DateTime $expirationDate;

	#[ORM\Column(type: 'datetime', nullable: true)]
	private ?DateTime $invalidationDate;

	#[ORM\Column(type: 'boolean')]
	private bool $rememberMe;

	public function getId(): int {
		return $this->id;
	}

	public function getToken(): string {
		return $this->token;
	}

	public function getRefreshDate(): DateTime {
		return $this->refreshDate;
	}

	public function setRefreshDate(DateTime $refreshDate): void {
		$this->refreshDate = $refreshDate;
	}

	public function setToken(string $token): void {
		$this->token = $token;
	}

	public function getExpirationDate(): DateTime {
		return $this->expirationDate;
	}

	public function setExpirationDate(DateTime $expirationDate): void {
		$this->expirationDate = $expirationDate;
	}

	public function getInvalidationDate(): ?DateTime {
		return $this->invalidationDate;
	}

	public function setInvalidationDate(DateTime $invalidationDate): void {
		$this->invalidationDate = $invalidationDate;
	}

	public function isRememberMe(): bool {
		return $this->rememberMe;
	}

	public function setRememberMe(bool $rememberMe): void {
		$this->rememberMe = $rememberMe;
	}

	public function getDevice(): Device {
		return $this->device;
	}

	public function setDevice(Device $device): void {
		$this->device = $device;
	}
}
