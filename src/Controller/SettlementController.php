<?php

namespace App\Controller;

use App\Repository\SettlementRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[OA\Tag(name: 'Settlement')]
class SettlementController extends AbstractController {
	/**
	 * @Nelmio\Areas("settlement")
	 */
	#[Rest\Get("/api/settlements/{id<\d+>}/summary")]
	#[Rest\View(statusCode: 200)]
	#[OA\Response(
		response: 200,
		description: 'Returns the rewards of an user',
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
		description: 'The field used to order rewards',
		in: 'path',
		schema: new OA\Schema(type: 'integer')
	)]
	public function index(int $id, SettlementRepository $repository) {
		return ['data' => $repository->getSummary($id)];
	}
}
