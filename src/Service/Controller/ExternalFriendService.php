<?php

namespace App\Service\Controller;

use App\Entity\ExternalFriend;
use App\Exception\EntityNotFoundException;
use App\Repository\ExternalFriendRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExternalFriendService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly ExternalFriendRepository $externalFriendRepository,
		private readonly UserService $userService
	) {	}

	public function get(int $id): ExternalFriend {
		$friend = $this->externalFriendRepository->findOneBy([
			'id' => $id,
			'owner' => $this->userService->getCurrentUser(),
		]);

		if (!($friend instanceof ExternalFriend)) {
			throw new EntityNotFoundException('Not found an external friend');
		}
		return $friend;
	}

	public function create(ExternalFriend $friend): void {
		$friend->setOwner($this->userService->getCurrentUser());

		$this->entityManager->persist($friend);
		$this->entityManager->flush();
	}

	public function update(ExternalFriend $friend): void {
		$this->entityManager->persist($friend);
		$this->entityManager->flush();
	}

	public function delete(ExternalFriend $friend): void {
		$this->entityManager->remove($friend);
		$this->entityManager->flush();
	}
}
