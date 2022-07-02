<?php

namespace App\Entity;

use App\Repository\SettlementRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettlementRepository::class)]
class Settlement {
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', options: ['unsigned' => true])]
	private int $id;
	#[ORM\Column(type: 'string', length: 63)]
	private string $name;
}
