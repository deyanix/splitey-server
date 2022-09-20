<?php

namespace App\Controller;

use App\Entity\FriendInvitationStatus;
use App\Form\FriendInvitationForm;
use App\Model\Form\ExternalFriendData;
use App\Model\Form\FriendInvitationData;
use App\Model\PaginationData;
use App\Repository\FriendRepository;
use App\Service\Controller\FriendInvitationService;
use App\Service\Controller\FriendService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
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

	#[Rest\Get(path: '/invitations', name: 'get_initivations')]
	#[Rest\View(statusCode: 200, serializerGroups: ['friend_invitation:read', 'user:minimal'])]
	#[OA\Get(summary: 'Get unanswered invitations')]
	public function getIntivations() {
		return $this->invitationService->getInvitations();
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

	#[Rest\Post(path: '/invitations/answer', name: 'invite_answer')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Answer to invitation to friendship')]
	public function inviteAnswer() {
		return []; // TODO: Do it!
	}
}
