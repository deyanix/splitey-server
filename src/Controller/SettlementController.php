<?php

namespace App\Controller;

use App\Repository\SettlementRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Rest\Route('/settlements')]
#[OA\Tag(name: 'Settlement')]
class SettlementController extends AbstractController {
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
