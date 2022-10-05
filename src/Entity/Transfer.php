<?php

namespace App\Entity;

use App\Repository\TransferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

#[ORM\Entity(repositoryClass: TransferRepository::class)]
class Transfer {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;

	#[ORM\Column(type: 'string', length: 63)]
	#[Serializer\Groups(["transfer:read"])]
	private string $name;

	#[ORM\ManyToOne(targetEntity: SettlementMember::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: "paying_member_id", referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
	#[Serializer\Groups(["transfer:read"])]
	private SettlementMember $payingMember;

	#[ORM\OneToMany(targetEntity: TransferDivision::class, cascade: ['persist'], mappedBy: 'transfer')]
	#[Serializer\Groups(["transfer:read"])]
	private Collection $divisions;

	public function __construct() {
		$this->divisions = new ArrayCollection();
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

	public function getPayingMember(): SettlementMember {
		return $this->payingMember;
	}

	public function setPayingMember(SettlementMember $payingMember): void {
		$this->payingMember = $payingMember;
	}

	public function getDivisions(): Collection {
		return $this->divisions;
	}

	public function getDivision(SettlementMember $member): ?TransferDivision {
		return $this->getDivisions()
			->filter(fn ($division) => $division->getMember() === $member)
			->first() ?: null;
	}
}
