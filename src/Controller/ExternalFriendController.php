<?php

namespace App\Controller;

use App\Form\ExternalFriendForm;
use App\Model\Form\ExternalFriendData;
use App\Service\Controller\ExternalFriendService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/friends/external', name: 'external_friend_')]
#[OA\Tag(name: 'Friend')]
class ExternalFriendController extends AbstractController {
	public function __construct(
		private readonly FormService           $formService,
		private readonly ExternalFriendService $externalFriendService,
	) { }

	#[Rest\Get(path: '/{id<\d+>}', name: 'get')]
	#[Rest\View(statusCode: 200, serializerGroups: ['external_friend:read'])]
	#[OA\Get(summary: 'Get an external friend')]
	public function get(int $id) {
		return $this->externalFriendService->get($id);
	}

	#[Rest\Post(name: 'create')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(
		summary: 'Create an external friend',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Model(type: ExternalFriendData::class)
			)
		)
	)]
	public function create(Request $request) {
		$form = $this->formService->handle($request, ExternalFriendForm::class);
		$this->externalFriendService->create($form->getData()->toEntity());
	}

	#[Rest\Put(path: '/{id<\d+>}', name: 'update')]
	#[Rest\View(statusCode: 200)]
	#[OA\Put(
		summary: 'Update an external friend',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Model(type: ExternalFriendData::class)
			)
		)
	)]
	public function update(Request $request, int $id) {
		$externalFriend = $this->externalFriendService->get($id);
		$form = $this->formService->handle($request, ExternalFriendForm::class, ExternalFriendData::fromEntity($externalFriend));
		$this->externalFriendService->update($form->getData()->toEntity($externalFriend));
	}

	#[Rest\Delete(path: '/{id<\d+>}', name: 'delete')]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(summary: 'Delete an external friend')]
	public function delete(int $id) {
		$friend = $this->externalFriendService->get($id);
		$this->externalFriendService->delete($friend);
	}

	// TODO: External friend linking. Remember about link source (e.g. settlement member)
//	#[Rest\Post(path: '/{id<\d+>}/link', name: 'link')]
//	#[Rest\View(statusCode: 200)]
//	#[OA\Post(summary: 'Link an external friend to another external friend')]
//	public function link() {
//		return [];
//	}
//
//	#[Rest\Delete(path: '/{id<\d+>}/link', name: 'delete_link')]
//	#[Rest\View(statusCode: 200)]
//	#[OA\Delete(summary: 'Delete link to external friend')]
//	public function deleteLink() {
//		return [];
//	}
}
