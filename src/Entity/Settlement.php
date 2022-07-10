<?php

namespace App\Entity;

use App\Repository\SettlementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: SettlementRepository::class)]
class Settlement {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	#[Serializer\Groups(["settlement:read", "settlement:minimal:read"])]
	private int $id;

	#[ORM\Column(type: 'string', length: 63)]
	#[Assert\Type("string")]
	#[Assert\Length(min: 3, max: 63)]
	#[Serializer\Groups(["settlement:read", "settlement:minimal:read"])]
	private string $name;

	#[ORM\OneToMany(mappedBy: 'settlement', targetEntity: SettlementMember::class, cascade: ['persist'])]
	#[Serializer\Groups(["settlement:read"])]
	private Collection $members;

	public function __construct() {
		$this->members = new ArrayCollection();
	}

	public function getId(): int {
		return $this->id;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): void {
		$this->name = $name;
	}

	public function getMembers(): Collection {
		return $this->members;
	}
}
