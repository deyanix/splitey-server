<?php

namespace App\Model\Form;

use App\Entity\Settlement;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

class SettlementUpdateData {
	public static function fromEntity(Settlement $settlement): SettlementUpdateData {
		$data = new SettlementUpdateData();
		$data->setName($settlement->getName());
		return $data;
	}

	#[OA\Property('name', type: 'string')]
	#[Assert\Type("string")]
	#[Assert\Length(min: 3, max: 63)]
	private mixed $name;

	public function getName(): mixed {
		return $this->name;
	}

	public function setName(mixed $name): void {
		$this->name = $name;
	}

	public function toEntity(Settlement $settlement): Settlement {
		$settlement->setName($this->getName());
		return $settlement;
	}
}
