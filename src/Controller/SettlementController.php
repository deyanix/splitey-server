<?php

namespace App\Controller;

use App\Form\SettlementForm;
use App\Repository\SettlementRepository;
use App\Service\Controller\SettlementService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/settlements', name: 'settlement')]
#[OA\Tag(name: 'Settlement')]
class SettlementController extends AbstractController {
	public function __construct(
		private readonly FormService       $formService,
		private readonly SettlementService $settlementService,
	) { }

	#[Rest\Get(name: 'get_all')]
	#[Rest\View(statusCode: 200, serializerGroups: ["pagination_result", "settlement:minimal:read"])]
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
	public function getAll(int $offset, int $length) {
		return $this->settlementService->getUserSettlements($offset, $length);
	}

	#[Rest\Get("/{id<\d+>}", name: 'get')]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Get(summary: 'Get a settlement')]
	public function get(int $id) {
		return $this->settlementService->getUserSettlement($id);
	}

	#[Rest\Post(name: 'create')]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Post(summary: 'Create a settlement', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'name',
			type: 'string'
		)]
	)))]
	public function create(Request $request) {
		$form = $this->formService->handle($request, SettlementForm::class);
		return $this->settlementService->createSettlement($form->getData());
	}

	#[Rest\Put("/{id<\d+>}", name: 'update')]
	#[Rest\View(statusCode: 200, serializerGroups: ["settlement:read", "settlement_member:read"])]
	#[OA\Put(summary: 'Update a settlement', requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
		new OA\Property(
			property: 'name',
			type: 'string'
		)]
	)))]
	public function update(int $id, Request $request) {
		$settlement = $this->settlementService->getUserSettlement($id);
		$form = $this->formService->handle($request, SettlementForm::class, $settlement);
		return $this->settlementService->updateSettlement($form->getData());
	}

	#[Rest\Delete("/{id<\d+>}", name: 'delete')]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(summary: 'Delete a settlement')]
	public function delete(int $id, SettlementRepository $repository) {
		$settlement = $this->settlementService->getUserSettlement($id);
		$this->settlementService->deleteSettlement($settlement);
	}

	#[Rest\Get("/{id<\d+>}/summary", name: 'summary')]
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
	public function summary(int $id): array {
		return ['data' => $this->settlementService->getSummary($id)];
	}

	#[Rest\Get("/{id<\d+>}/arrangement", name: 'arrangement')]
	#[Rest\QueryParam('optimized', default: false)]
	#[Rest\View(statusCode: 200)]
	#[OA\Get(summary: 'Gets a settlement arrangement')]
	#[OA\Parameter(
		name: 'id',
		description: 'Identifier of the settlement',
		in: 'path',
		schema: new OA\Schema(type: 'integer')
	)]
	#[OA\Parameter(
		name: 'optimized',
		description: 'If true, returns optimized arrangement',
		in: 'query',
		schema: new OA\Schema(type: 'boolean')
	)]
	public function arrangement(int $id, string $optimized): array {
		if ($optimized === 'true') {
			return ['data' => $this->settlementService->getOptimizedArrangement($id)];
		}
		return ['data' => $this->settlementService->getArrangement($id)];
	}
}
