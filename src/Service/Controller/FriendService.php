<?php

namespace App\Service\Controller;

use App\Repository\FriendRepository;
use Doctrine\ORM\EntityManagerInterface;

class FriendService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly FriendRepository $friendRepository,
		private readonly UserService $userService
	) {	}

	public function getFriends(): array {
		return $this->friendRepository->getUserFriends($this->userService->getCurrentUser());
	}
}
