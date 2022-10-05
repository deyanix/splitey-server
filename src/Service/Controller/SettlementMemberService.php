<?php

namespace App\Service\Controller;

use App\Entity\ExternalFriend;
use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Entity\User;
use App\Repository\SettlementMemberRepository;
use App\Repository\SettlementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SettlementMemberService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly SettlementRepository   $settlementRepository,
		private readonly SettlementMemberRepository   $memberRepository,
		private readonly UserService   $userService,
		private readonly FriendService   $friendService,
	) {	}

	public function hasUserMember(Settlement $settlement, User $user): bool {
		return $this->settlementRepository->isUserMember($settlement->getId(), $user);
	}

	public function addUserMember(Settlement $settlement, int $userId): void {
		$user = $this->entityManager->getReference(User::class, $userId);
		if ($this->hasUserMember($settlement, $user)) {
			throw new BadRequestException('User is this settlement\'s member');
		}
		if ($this->friendService->hasUserFriend($user)) {
			throw new BadRequestException('Requested user is\'t current user\'s friend');
		}

		$member = new SettlementMember();
		$member->setUser($user);
		$member->setSettlement($settlement);

		$this->entityManager->persist($member);
		$this->entityManager->flush();
	}

	public function hasExternalFriendMember(Settlement $settlement, ExternalFriend $friend): bool {
		return $this->settlementRepository->isExternalFriendMember($settlement->getId(), $friend);
	}

	public function addExternalFriendMember(Settlement $settlement, int $friendId): void {
		$externalFriend = $this->entityManager->getReference(ExternalFriend::class, $friendId);
		if ($this->hasExternalFriendMember($settlement, $externalFriend)) {
			throw new BadRequestException('External friend already is this settlement\'s member');
		}
		if ($externalFriend->getOwner() !== $this->userService->getCurrentUser()) {
			throw new BadRequestException('Requested user is\'t external friend\'s owner');
		}

		$member = new SettlementMember();
		$member->setExternalFriend($externalFriend);
		$member->setSettlement($settlement);

		$this->entityManager->persist($member);
		$this->entityManager->flush();
	}

	public function removeMember(int $memberId): void {
		$member = $this->memberRepository->find($memberId);
		$settlement = $member->getSettlement();
		if (!$this->hasUserMember($settlement, $this->userService->getCurrentUser())) {
			throw new BadRequestException('Current user don\'t have an access to requested settlement');
		}

		$member->setExternalFriend(null);
		$member->setUser(null);
		$this->entityManager->persist($member);
		$this->entityManager->flush();
	}
}
