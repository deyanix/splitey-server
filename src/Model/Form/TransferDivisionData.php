<?php

namespace App\Model\Form;

use App\Entity\SettlementMember;
use App\Entity\TransferDivision;
use Doctrine\Common\Collections\Collection;
use OpenApi\Attributes as OA;

class TransferDivisionData {
	public static function fromEntities(Collection $collection): array {
		return $collection
			->map(fn ($entity) => self::fromEntity($entity))
			->toArray();
	}

	public static function fromEntity(TransferDivision $division): TransferDivisionData {
		$data = new TransferDivisionData();
		$data->setMember($division->getMember());
		$data->setAmount($division->getAmount());
		return $data;
	}

	#[OA\Property('memberId', type: 'integer')]
	private mixed $member;

	#[OA\Property('amount', type: 'number', format: 'float')]
	private mixed $amount;

	/**
	 * @return SettlementMember
	 */
	public function getMember(): mixed {
		return $this->member;
	}

	/**
	 * @param SettlementMember $member
	 * @return void
	 */
	public function setMember(mixed $member): void {
		$this->member = $member;
	}

	public function getAmount(): mixed {
		return $this->amount;
	}

	public function setAmount(mixed $amount): void {
		$this->amount = $amount;
	}
}
