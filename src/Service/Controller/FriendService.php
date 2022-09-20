<?php

namespace App\Service\Controller;

use App\Entity\Friend;
use App\Model\Form\DeleteFriendData;
use App\Repository\FriendRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FriendService {
	public function __construct(
		private readonly EntityManagerInterface $entityManager,
		private readonly FriendRepository $friendRepository,
		private readonly UserService $userService
	) {	}

	public function getFriends(): array {
		return $this->friendRepository->getUserFriends($this->userService->getCurrentUser());
	}

	public function deleteFriend(DeleteFriendData $data): void {
		$friend = $this->friendRepository->getUserFriend(
			$this->userService->getCurrentUser(),
			$data->getUser()
		);

		if (!($friend instanceof Friend)) {
			throw new BadRequestException('User is\'t your friend');
		}
		$this->entityManager->remove($friend);
		$this->entityManager->flush();
	}
}
