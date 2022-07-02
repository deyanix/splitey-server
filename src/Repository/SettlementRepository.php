<?php

namespace App\Repository;

use App\Entity\Settlement;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SettlementRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Settlement::class);
	}

	public function getSummary(int $settlementId): array {
		$statement = $this->prepareQuery('Settlement/Summary');
		$statement->bindValue('settlement_id', $settlementId);
		return $statement->executeQuery()->fetchAllAssociative();
	}
}

