<?php

namespace App\Service;

use App\Repository\SettlementRepository;

class SettlementService {
	private SettlementRepository $repository;

	public function __construct(SettlementRepository $repository) {
		$this->repository = $repository;
	}

	public function getArrangement(int $id): array {
		$summary = $this->repository->getSummary($id);
		$optimizer = new SettlementArrangementOptimizer($summary);
		return $optimizer->optimize();
	}
}
