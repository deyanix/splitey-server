<?php

namespace App\Controller;

use App\Entity\FriendInvitationStatus;
use App\Form\DeleteFriendForm;
use App\Form\FriendInvitationForm;
use App\Model\Form\DeleteFriendData;
use App\Model\Form\FriendInvitationData;
use App\Service\Controller\FriendInvitationService;
use App\Service\Controller\FriendService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/friends', name: 'friend_')]
#[OA\Tag(name: 'Friend')]
class FriendController extends AbstractController {
	public function __construct(
		private readonly FriendService $friendService,
		private readonly FriendInvitationService $invitationService,
		private readonly FormService $formService,
	) {}

	#[Rest\Get(name: 'get_all')]
	#[Rest\View(statusCode: 200)]
	#[OA\Get(summary: 'Get friends and external friends')]
	public function getFriends() {
		return $this->friendService->getFriends();
	}

	#[Rest\Delete(name: 'delete')]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(
		summary: 'Delete a friend',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Model(type: DeleteFriendData::class)
			)
		)
	)]
	public function deleteFriend(Request $request) {
		$form = $this->formService->handle($request, DeleteFriendForm::class);
		$this->friendService->deleteFriend($form->getData());
	}

	#[Rest\Get(path: '/invitations', name: 'get_invitations')]
	#[Rest\View(statusCode: 200, serializerGroups: ['friend_invitation:read', 'user:minimal'])]
	#[OA\Get(summary: 'Get unanswered invitations')]
	public function getIntivations() {
		return $this->invitationService->getInvitations();
	}

	#[Rest\Get(path: '/invitations/sent', name: 'get_sent_invitations')]
	#[Rest\View(statusCode: 200, serializerGroups: ['friend_invitation:read', 'user:minimal'])]
	#[OA\Get(summary: 'Get sent invitations')]
	public function getSentIntivations() {
		return $this->invitationService->getSentInvitations();
	}

	#[Rest\Post(path: '/invitations', name: 'invite')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(
		summary: 'Invite a user to friendship',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Model(type: FriendInvitationData::class)
			)
		)
	)]
	public function invite(Request $request) {
		$form = $this->formService->handle($request, FriendInvitationForm::class);
		$this->invitationService->invite($form->getData());
	}

	#[Rest\Post(path: '/invitations/{id<\d+>}/accept', name: 'accept_invitation')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Accept invitation to friendship')]
	public function acceptInvitation(int $id) {
		$this->invitationService->answer($id, true);
	}

	#[Rest\Post(path: '/invitations/{id<\d+>}/decline', name: 'decline_invitation')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Decline invitation to friendship')]
	public function declineInvitation(int $id) {
		$this->invitationService->answer($id, false);
	}

	#[Rest\Post(path: '/invitations/see', name: 'see_invitations')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'See all invitations to friendship')]
	public function seeInvitations() {
		$this->invitationService->see();
	}
}
