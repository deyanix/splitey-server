<?php

namespace App\Service\Controller;

use App\Entity\Friend;
use App\Entity\FriendInvitation;
use App\Model\Form\FriendInvitationData;
use App\Repository\FriendInvitationRepository;
use App\Repository\FriendRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FriendInvitationService {
	public function __construct(
		private readonly EntityManagerInterface     $entityManager,
		private readonly FriendRepository           $friendRepository,
		private readonly FriendInvitationRepository $invitationRepository,
		private readonly UserService                $userService
	) {	}

	public function getInvitations(): array {
		return $this->invitationRepository->getActiveInvitationsFor(
			$this->userService->getCurrentUser()
		);
	}

	public function getSentInvitations(): array {
		return $this->invitationRepository->getSentInvitationsFor(
			$this->userService->getCurrentUser()
		);
	}

	public function invite(FriendInvitationData $data): void {
		$currentUser = $this->userService->getCurrentUser();
		$recipient = $data->getRecipient();

		$currentFriend = $this->friendRepository->getUserFriend($currentUser, $recipient);
		if ($currentFriend instanceof Friend) {
			throw new BadRequestException('User is already your friend');
		}

		$currentInvitation = $this->invitationRepository->getActiveInvitationFor(
			$currentUser,
			$recipient
		);

		if ($currentInvitation instanceof FriendInvitation) {
			throw new BadRequestException('Invitation has already sent to recipient');
		}

		$invitation = new FriendInvitation();
		$invitation->setSender($currentUser);
		$invitation->setRecipient($recipient);
		$invitation->setDate(new DateTime());
		$this->entityManager->persist($invitation);
		$this->entityManager->flush();
	}

	public function answer(int $id, bool $accepted): void {
		$currentUser = $this->userService->getCurrentUser();
		$invitation = $this->invitationRepository->getActiveInvitation($id);
		if (!($invitation instanceof FriendInvitation) || $invitation->getRecipient() !== $currentUser) {
			throw new NotFoundHttpException('Not found invitation');
		}

		$invitation->setActive(false);
		$this->entityManager->persist($invitation);

		$currentFriend = $this->friendRepository->getUserFriend(
			$invitation->getSender(),
			$invitation->getRecipient()
		);

		if ($accepted && !($currentFriend instanceof Friend)) {
			$friend = new Friend();
			$friend->setUser1($invitation->getSender());
			$friend->setUser2($invitation->getRecipient());
			$this->entityManager->persist($friend);
		}

		$this->entityManager->flush();
	}

	public function see(): void {
		$this->invitationRepository->seeActiveInvitationsFor(
			$this->userService->getCurrentUser()
		);
	}
}
