<?php

namespace App\Service\Controller;

use App\Entity\Friend;
use App\Entity\User;
use App\Repository\FriendRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FriendService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly FriendRepository $friendRepository,
		private readonly UserService $userService
	) {	}

	public function getUsers(string $name): array {
		// TODO: copied from UserService
		$trimName = preg_replace('/\s+/', ' ', trim($name));
		if (strlen($trimName) < 3) {
			return [];
		}

		return $this->friendRepository->searchUsers($name, $this->userService->getCurrentUser()->getId());
	}

	public function hasUserFriend(User $user): bool {
		$friend = $this->friendRepository->getUserFriend($this->userService->getCurrentUser(), $user);
		return $friend !== null;
	}

	public function getFriends(): array {
		return $this->friendRepository->getUserFriends($this->userService->getCurrentUser());
	}

	public function deleteFriend(User $user): void {
		$friend = $this->friendRepository->getUserFriend(
			$this->userService->getCurrentUser(),
			$user
		);

		if (!($friend instanceof Friend)) {
			throw new BadRequestException('User is\'t your friend');
		}
		$this->entityManager->remove($friend);
		$this->entityManager->flush();
	}
}
