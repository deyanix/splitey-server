<?php

namespace App\Service\Controller;

use App\Entity\FriendInvitation;
use App\Entity\FriendInvitationStatus;
use App\Entity\User;
use App\Model\Form\FriendInvitationData;
use App\Repository\FriendInvitationRepository;
use App\Repository\FriendRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class FriendInvitationService {
	public function __construct(
		private readonly EntityManagerInterface     $entityManager,
		private readonly FriendRepository           $friendRepository,
		private readonly FriendInvitationRepository $invitationRepository,
		private readonly UserService                $userService
	) {	}

	public function getInvitations(): array {
		return $this->invitationRepository->findBy([
			'recipient' => $this->userService->getCurrentUser(),
			'status' => [FriendInvitationStatus::PENDING, FriendInvitationStatus::SEEN]
		]);
	}

	public function getInvitation(User $recipient): ?FriendInvitation {
		return $this->getInvitation($this->userService->getCurrentUser(), $recipient);
	}

	public function invite(FriendInvitationData $data): void {
		$currentUser = $this->userService->getCurrentUser();
		$recipient = $data->getRecipient();

		if ($this->friendRepository->hasUserFriend($currentUser, $recipient)) {
			throw new BadRequestException('User is already your friend');
		}

		$openedInvitation = $this->invitationRepository->getActiveInvitation(
			$currentUser,
			$recipient
		);

		if ($openedInvitation instanceof FriendInvitation) {
			throw new BadRequestException('Invitation has already sent to recipient');
		}

		$invitation = new FriendInvitation();
		$invitation->setSender($currentUser);
		$invitation->setRecipient($recipient);
		$invitation->setDate(new DateTime());
		$this->entityManager->persist($invitation);
		$this->entityManager->flush();
	}
}
