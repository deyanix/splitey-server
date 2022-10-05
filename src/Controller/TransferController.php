<?php

namespace App\Controller;

use App\Form\TransferForm;
use App\Model\Form\TransferData;
use App\Service\Controller\TransferService;
use App\Service\FormService;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Rest\Route('/settlements/transfers', name: 'settlement_transfers')]
#[OA\Tag(name: 'Settlement')]
class TransferController extends AbstractController {
	public function __construct(
		private readonly TransferService $transferService,
		private readonly FormService $formService,
	) {}

	#[Rest\Post(name: 'create')]
	#[Rest\View(statusCode: 200)]
	#[OA\Post(
		summary: 'Create a transfer',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Nelmio\Model(type: TransferData::class)
			)
		)
	)]
	public function create(Request $request) {
		$form = $this->formService->handle($request, TransferForm::class);
		$this->transferService->createTransfer($form->getData());
	}

	#[Rest\Put('/{id<\d+>}', name: 'update')]
	#[Rest\View(statusCode: 200)]
	#[OA\Put(
		summary: 'Update a transfer',
		requestBody: new OA\RequestBody(
			content: new OA\JsonContent(
				ref: new Nelmio\Model(type: TransferData::class)
			)
		)
	)]
	public function update(Request $request, int $id) {
		$transfer = $this->transferService->getTransfer($id);
		$form = $this->formService->handle($request, TransferForm::class, TransferData::fromEntity($transfer));
		$this->transferService->updateTransfer($form->getData(), $transfer);
	}

	#[Rest\Delete('/{id<\d+>}', name: 'delete')]
	#[Rest\View(statusCode: 200)]
	#[OA\Delete(summary: 'Delete a transfer')]
	public function delete(int $id) {
		$transfer = $this->transferService->getTransfer($id);
		$this->transferService->removeTransfer($transfer);
	}
}
