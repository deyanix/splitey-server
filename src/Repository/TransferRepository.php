<?php

namespace App\Repository;

use App\Entity\Transfer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TransferRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Transfer::class);
	}

	public function getTransfersBySettlement(int $settlementId): array {
		return $this->createQueryBuilder('t')
			->innerJoin('t.payingMember', 'pm')
			->where('pm.settlement = :settlementId')
			->setParameter('settlementId', $settlementId)
			->getQuery()
			->getResult();
	}
}

