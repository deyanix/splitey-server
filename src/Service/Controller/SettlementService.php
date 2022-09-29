<?php

namespace App\Service\Controller;

use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Exception\EntityNotFoundException;
use App\Model\PaginationResult;
use App\Repository\SettlementRepository;
use App\Service\Settlement\SettlementArrangementOptimizer;
use Doctrine\ORM\EntityManagerInterface;

class SettlementService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly SettlementRepository   $settlementRepository,
		private readonly UserService            $userService,
	) {	}

	public function getUserSettlements(int $offset, int $length): PaginationResult {
		return $this->settlementRepository->findByUser($this->userService->getCurrentUser(), $offset, $length);
	}

	public function getUserSettlement(int $id): Settlement {
		$settlement = $this->settlementRepository->findOneByUser($id, $this->userService->getCurrentUser());
		if ($settlement === null) {
			throw new EntityNotFoundException('Not found entity');
		}
		return $settlement;
	}

	public function createSettlement(Settlement $settlement): Settlement {
		$member = new SettlementMember();
		$member->setUser($this->userService->getCurrentUser());
		$member->setSettlement($settlement);
		$settlement->getMembers()->add($member);

		$this->entityManager->persist($settlement);
		$this->entityManager->persist($member);
		$this->entityManager->flush();
		return $settlement;
	}

	public function updateSettlement(Settlement $settlement): Settlement {
		$this->entityManager->persist($settlement);
		$this->entityManager->flush();
		return $settlement;
	}

	public function deleteSettlement(Settlement $settlement): void {
		$this->entityManager->remove($settlement);
		$this->entityManager->flush();
	}

	public function getSummary(int $id): array {
		return $this->settlementRepository->getSummary($id);
	}

	public function getArrangement(int $id): array {
		return $this->settlementRepository->getArrangement($id);
	}

	public function getOptimizedArrangement(int $id): array {
		$summary = $this->settlementRepository->getSummary($id);
		$optimizer = new SettlementArrangementOptimizer($summary);
		return $optimizer->optimize();
	}
}
