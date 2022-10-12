<?php

namespace App\Model\Form;

use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SettlementCreateData {
	#[OA\Property('name', type: 'string')]
	#[Assert\Type("string")]
	#[Assert\Length(min: 3, max: 63)]
	private mixed $name;

	#[OA\Property(type: 'array', items:
		new OA\Items(new Nelmio\Model(type: SettlementMemberData::class))
	)]
	private mixed $members;

	public function getName(): mixed {
		return $this->name;
	}

	public function setName(mixed $name): void {
		$this->name = $name;
	}

	public function getMembers(): mixed {
		return $this->members;
	}

	public function setMembers(mixed $members): void {
		$this->members = $members;
	}
}
