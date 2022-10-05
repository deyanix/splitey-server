<?php

namespace App\Controller;

use App\Service\Controller\SettlementMemberService;
use App\Service\Controller\SettlementService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Rest\Route('/settlements/members', name: 'settlement_member')]
#[OA\Tag(name: 'Settlement')]
class SettlementMemberController extends AbstractController {
	public function __construct(
		private readonly SettlementService $settlementService,
		private readonly SettlementMemberService $memberService,
	) { }

	#[Rest\Post("/user", name: 'add_user')]
	#[Rest\RequestParam('settlementId', requirements: '\d+', nullable: true)]
	#[Rest\RequestParam('userId', requirements: '\d+', nullable: true)]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Add a member to settlement')]
	public function addUser(int $settlementId, int $userId) {
		$settlement = $this->settlementService->getUserSettlement($settlementId);
		$this->memberService->addUserMember($settlement, $userId);
	}

	#[Rest\Post("/external-friend", name: 'add_external_friend')]
	#[Rest\RequestParam('settlementId', requirements: '\d+', nullable: true)]
	#[Rest\RequestParam('externalFriendId', requirements: '\d+', nullable: true)]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(summary: 'Add an external friend to settlement')]
	public function addExternalFriendMember(int $settlementId, int $externalFriendId) {
		$settlement = $this->settlementService->getUserSettlement($settlementId);
		$this->memberService->addExternalFriendMember($settlement, $externalFriendId);
	}

	#[Rest\Delete("/{memberId<\d+>}", name: 'remove')]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(summary: 'Delete a member to settlement')]
	public function removerMember(int $memberId) {
		$this->memberService->removeMember($memberId);
	}
}
