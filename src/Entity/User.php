<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'string', length: 31, unique: true)]
	private string $username;

	#[ORM\Column(type: 'string', length: 95)]
	private string $password;

	#[ORM\Column(type: 'string', length: 63)]
	private string $email;

	#[ORM\Column(type: 'string', length: 31)]
	private string $firstName;

	#[ORM\Column(type: 'string', length: 63)]
	private string $lastName;

	#[ORM\ManyToMany(targetEntity: ExternalContact::class, cascade: ['persist'])]
	#[ORM\JoinTable(
		name: "user_external_contact",
		joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
		inverseJoinColumns: [new ORM\JoinColumn(name: "external_contact_id", referencedColumnName: "id")]
	)]
	private Collection $externalContacts;

	#[ORM\ManyToMany(targetEntity: User::class, cascade: ['persist'])]
	#[ORM\JoinTable(
		name: "user_internal_contact",
		joinColumns: [new ORM\JoinColumn(name: "user_id", referencedColumnName: "id")],
		inverseJoinColumns: [new ORM\JoinColumn(name: "contact_user_id", referencedColumnName: "id")]
	)]
	private Collection $internalContacts;

	public function getId(): int {
		return $this->id;
	}

	public function getUsername(): string {
		return $this->username;
	}

	public function setUsername(string $username): void {
		$this->username = $username;
	}

	public function getEmail(): string {
		return $this->email;
	}

	public function setEmail(string $email): void {
		$this->email = $email;
	}

	public function getFirstName(): string {
		return $this->firstName;
	}

	public function setFirstName(string $firstName): void {
		$this->firstName = $firstName;
	}

	public function getLastName(): string {
		return $this->lastName;
	}

	public function setLastName(string $lastName): void {
		$this->lastName = $lastName;
	}

	public function getPassword(): ?string {
		return $this->password;
	}

	public function setPassword(string $password): void {
		$this->password = $password;
	}

	public function getRoles(): array {
		return [];
	}

	public function eraseCredentials() {
		// Nothing.
	}

	public function getUserIdentifier(): string {
		return $this->username;
	}
}
