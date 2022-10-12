<?php

namespace App\Service\Controller;

use App\Entity\ExternalFriend;
use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Model\Form\SettlementCreateData;
use App\Model\PaginationResult;
use App\Repository\SettlementRepository;
use App\Service\Settlement\SettlementArrangementOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SettlementService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly SettlementRepository   $settlementRepository,
		private readonly UserService            $userService,
		private readonly FriendService          $friendService,
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

	public function createSettlement(SettlementCreateData $data): Settlement {
		$settlement = new Settlement();
		$settlement->setName($data->getName());
		$this->entityManager->persist($settlement);

		$member = new SettlementMember();
		$member->setUser($this->userService->getCurrentUser());
		$member->setSettlement($settlement);
		$member->setActive(true);
		$settlement->getMembers()->add($member);
		$this->entityManager->persist($member);

		foreach ($data->getMembers() as $memberData) {
			/** @var User|null $user */
			$user = $memberData->getUser();
			if ($user !== null) {
				if ($settlement->getMembers()->filter(fn ($member) => $member->getUser() === $user)->count() !== 0) {
					throw new BadRequestException('User is already added to this settlement');
				}
				if ($this->friendService->hasUserFriend($user)) {
					throw new BadRequestException('Requested user is\'t current user\'s friend');
				}
			}

			/** @var ExternalFriend|null $externalFriend */
			$externalFriend = $memberData->getExternalFriend();
			if ($externalFriend !== null ) {
				if ($settlement->getMembers()->filter(fn ($member) => $member->getExternalFriend() === $externalFriend)->count() !== 0) {
					throw new BadRequestException('External friend is already added to this settlement');
				}
				if ($externalFriend->getOwner() !== $this->userService->getCurrentUser()) {
					throw new BadRequestException('Requested user is\'t external friend\'s owner');
				}
			}


			$member = new SettlementMember();
			$member->setUser($memberData->getUser());
			$member->setExternalFriend($memberData->getExternalFriend());
			$member->setSettlement($settlement);
			$member->setActive(true);
			$settlement->getMembers()->add($member);
			$this->entityManager->persist($member);
		}

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
