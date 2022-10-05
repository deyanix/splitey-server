<?php

namespace App\Model\Form;

use App\Entity\SettlementMember;
use App\Entity\Transfer;
use App\Entity\TransferDivision;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;

class TransferData {
	public static function fromEntity(Transfer $transfer): TransferData {
		$data = new TransferData();
		$data->setName($transfer->getName());
		$data->setPayingMember($transfer->getPayingMember());
		$data->setDivisions(TransferDivisionData::fromEntities($transfer->getDivisions()));
		return $data;
	}

	#[OA\Property('name', type: 'string')]
	private mixed $name;

	#[OA\Property('divisions', type: 'array', items:
		new OA\Items(new Nelmio\Model(type: TransferDivisionData::class))
	)]
	private mixed $divisions;

	#[OA\Property('payingMemberId', type: 'integer')]
	private mixed $payingMember;

	public function getName(): mixed {
		return $this->name;
	}

	public function setName(mixed $name): void {
		$this->name = $name;
	}

	/**
	 * @return array<TransferDivisionData>
	 */
	public function getDivisions(): mixed {
		return $this->divisions;
	}

	/**
	 * @return TransferDivision|null
	 */
	public function getDivision(SettlementMember $member): mixed {
		return current(array_filter(
			$this->getDivisions(),
			fn ($division) => $division->getMember() === $member,
		)) ?: null;
	}

	public function setDivisions(mixed $divisions): void {
		$this->divisions = $divisions;
	}

	/**
	 * @return SettlementMember
	 */
	public function getPayingMember(): mixed {
		return $this->payingMember;
	}

	/**
	 * @param SettlementMember $payingMember
	 * @return void
	 */
	public function setPayingMember(mixed $payingMember): void {
		$this->payingMember = $payingMember;
	}
}
