<?php

namespace App\Service\Controller;

use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Repository\SettlementRepository;
use App\Service\Settlement\SettlementArrangementOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SettlementService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly SettlementRepository   $settlementRepository,
		private readonly UserService            $userService,
	) {	}

	public function getUserSettlements(int $offset, int $length): array {
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

	public function hasMember(Settlement $settlement, User $user): bool {
		return $this->settlementRepository->isSettlementMember($settlement->getId(), $user);
	}

	public function addUserMember(Settlement $settlement, int $userId): void {
		$user = $this->entityManager->getReference(User::class, $userId);
		if ($this->hasMember($settlement, $user)) {
			throw new BadRequestException('User is this settlement\'s member');
		}

		$member = new SettlementMember();
		$member->setUser($user);
		$member->setSettlement($settlement);

		$this->entityManager->persist($settlement);
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
