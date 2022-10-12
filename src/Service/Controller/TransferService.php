<?php

namespace App\Service\Controller;

use App\Entity\Settlement;
use App\Entity\Transfer;
use App\Entity\TransferDivision;
use App\Model\Form\TransferData;
use App\Repository\TransferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class TransferService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly TransferRepository $transferRepository,
		private readonly SettlementMemberService $memberService,
		private readonly UserService $userService
	) { }

	public function getTransfers(Settlement $settlement): array {
		return $this->transferRepository->getTransfersBySettlement($settlement->getId());
	}

	public function getTransfer(int $id): Transfer {
		$transfer = $this->transferRepository->find($id);
		$settlement = $transfer->getPayingMember()->getSettlement();
		if (!$this->memberService->hasUserMember($settlement, $this->userService->getCurrentUser())) {
			throw new BadRequestException('No access to settlement');
		}

		return $transfer;
	}

	private function convertDataToEntity(TransferData $data, Transfer $transfer): Transfer {
		$transfer->setName($data->getName());
		$transfer->setPayingMember($data->getPayingMember());

		$settlement = $data->getPayingMember()->getSettlement();
		foreach ($settlement->getMembers() as $member) {
			$divisionData = $data->getDivision($member);
			$divisionEntity = $transfer->getDivision($member);
			if ($divisionEntity === null) {
				$divisionEntity = new TransferDivision();
				$transfer->getDivisions()->add($divisionEntity);
			}

			$divisionEntity->setTransfer($transfer);
			$divisionEntity->setAmount($divisionData?->getAmount() ?? 0);
			$divisionEntity->setMember($member);
		}
		return $transfer;
	}

	private function persistTransfer(TransferData $data, Transfer $transfer) {
		$settlement = $data->getPayingMember()->getSettlement();
		if (!$this->memberService->hasUserMember($settlement, $this->userService->getCurrentUser())) {
			throw new BadRequestException('No access to settlement');
		}
		foreach ($data->getDivisions() as $division) {
			if ($division->getMember()->getSettlement() !== $settlement) {
				throw new BadRequestException('Member doesn\'t belong to settlement');
			}
		}

		$this->entityManager->persist($this->convertDataToEntity($data, $transfer));
		$this->entityManager->flush();
	}

	public function createTransfer(TransferData $data): void {
		$this->persistTransfer($data, new Transfer());
	}

	public function updateTransfer(TransferData $data, Transfer $transfer): void {
		$this->persistTransfer($data, $transfer);
	}

	public function removeTransfer(Transfer $transfer): void {
		$this->entityManager->remove($transfer);
		$this->entityManager->flush();
	}
}
