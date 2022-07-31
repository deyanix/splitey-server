<?php

namespace App\Controller;

use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Entity\User;
use App\Exception\EntityNotFoundException;
use App\Exception\FormValidationException;
use App\Form\SettlementForm;
use App\Repository\SettlementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Rest\Route('/settlements')]
#[OA\Tag(name: 'Settlement')]
class SettlementController extends AbstractController {
	private FormFactoryInterface $formFactory;
	private EntityManagerInterface $entityManager;

	public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $entityManager) {
		$this->formFactory = $formFactory;
		$this->entityManager = $entityManager;
	}

	#[Rest\Get]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:minimal:read"])]
	#[Rest\QueryParam('offset', requirements: '\d+', default: 0)]
	#[Rest\QueryParam('length', requirements: '\d+', default: 20)]
	#[OA\Get(summary: 'Get user\'s settlements')]
	#[OA\Parameter(
		name: 'offset',
		description: 'Offset records',
		in: 'query',
		schema: new OA\Schema(type: 'integer')
	)]
	#[OA\Parameter(
		name: 'length',
		description: 'Length records',
		in: 'query',
		schema: new OA\Schema(type: 'integer')
	)]
	public function getAll(int $offset, int $length, SettlementRepository $repository) {
		return [
			'data' => $repository->findByUser($this->getUser(), $offset, $length)
		];
	}

	#[Rest\Get("/{id<\d+>}")]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Get(summary: 'Get a settlement')]
	public function get(int $id, SettlementRepository $repository) {
		return $repository->findOneByUser($id, $this->getUser());
	}

	#[Rest\Post]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Post(summary: 'Create a settlement', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'name',
			type: 'string'
		)]
	)))]
	public function create(Request $request) {
		$form = $this->formFactory->create(SettlementForm::class);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}

		$settlement = $form->getData();
		$member = new SettlementMember();
		$member->setUser($this->getUser());
		$member->setSettlement($settlement);
		$settlement->getMembers()->add($member);

		$this->entityManager->persist($settlement);
		$this->entityManager->persist($member);
		$this->entityManager->flush();
		return $form->getData();
	}

	#[Rest\Put("/{id<\d+>}")]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Put(summary: 'Update a settlement', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'name',
			type: 'string'
		)]
	)))]
	public function update(int $id, Request $request,  SettlementRepository $repository) {
		$settlement = $repository->findOneByUser($id, $this->getUser());
		if ($settlement === null) {
			throw new EntityNotFoundException('Not found entity');
		}
		$form = $this->formFactory->create(SettlementForm::class, $settlement);

		$form->submit($request->request->all());
		if (!$form->isValid()) {
			throw new FormValidationException($form);
		}

		$this->entityManager->persist($form->getData());
		$this->entityManager->flush();
		return $form->getData();
	}

	#[Rest\Delete("/{id<\d+>}")]
	#[Rest\Post]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(summary: 'Delete a settlement')]
	public function delete(int $id, SettlementRepository $repository) {
		$settlement = $repository->findOneByUser($id, $this->getUser());
		if ($settlement === null) {
			throw new NotFoundHttpException('Not found entity');
		}

		$this->entityManager->remove($settlement);
		$this->entityManager->flush();
	}

	#[Rest\Put("/{id<\d+>}/members/user")]
	#[Rest\RequestParam('userId', requirements: '\d+', nullable: true)]
	#[Rest\View(statusCode: 200)]
//	#[OA\Put(summary: 'Add a member to settlement')]
	public function addUserMember(int $id, int $userId, SettlementRepository $repository) {
		$settlement = $repository->findOneByUser($id, $this->getUser());
		if ($settlement === null) {
			throw new EntityNotFoundException('Not found entity');
		}
		$member = new SettlementMember();
		$member->setUser($this->entityManager->getReference(User::class, $userId));
		$member->setSettlement($settlement);

		$this->entityManager->persist($settlement);
		$this->entityManager->flush();
	}

	#[Rest\Get("/{id<\d+>}/summary")]
	#[Rest\View(statusCode: 200)]
	#[OA\Get(summary: 'Gets a settlement summary')]
	#[OA\Response(
		response: 200,
		description: 'Returns the summary of settlement',
		content: new OA\JsonContent(
			properties: [
				new OA\Property(
					property: 'data',
					type: 'array',
					items: new OA\Items(
						properties: [
							new OA\Property(property: 'member_id', type: 'integer'),
							new OA\Property(property: 'balance', type: 'float'),
						],
						type: 'object'
					)
				)
			],
			type: 'object'
		)
	)]
	#[OA\Parameter(
		name: 'id',
		description: 'Identifier of the settlement',
		in: 'path',
		schema: new OA\Schema(type: 'integer')
	)]
	public function summary(int $id, SettlementRepository $repository): array {
		return ['data' => $repository->getSummary($id)];
	}
}
