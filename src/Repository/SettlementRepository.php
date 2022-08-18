<?php

namespace App\Repository;

use App\Entity\Settlement;
use App\Entity\User;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class SettlementRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Settlement::class);
	}

	public function findByUser(User $user, int $offset, int $length): array {
		$rsm = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata(Settlement::class, 's');

		$rsmTotal = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsmTotal->addScalarResult('total', 'total', 'integer');

		$query = $this->prepareNativeQuery('Search', $rsm)
			->setParameter('user_id', $user->getId())
			->setParameter('offset', $offset)
			->setParameter('length', $length);

		return [
			'rows' => $query->getResult(),
			'total' => $query->setResultSetMapping($rsmTotal)->getResult()[0]['total'] ?? 0
		];
	}

	public function findOneByUser(int $id, User $user): ?Settlement {
		return $this->createQueryBuilder('s')
			->innerJoin('s.members', 'sm', '')
			->where('s.id = :id')
			->andWhere('sm.user = :user')
			->setParameter('id', $id)
			->setParameter('user', $user)
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult();
	}

	public function isSettlementMember(int $id, User $user): bool {
		return $this->createQueryBuilder('s')
			->select('1')
			->innerJoin('s.members', 'sm', '')
			->where('s.id = :id')
			->andWhere('sm.user = :user')
			->setParameter('id', $id)
			->setParameter('user', $user)
			->setMaxResults(1)
			->getQuery()
			->getSingleScalarResult() === 1;
	}

	public function getSummary(int $settlementId): array {
		$statement = $this->prepareQuery('Settlement/Summary');
		$statement->bindValue('settlement_id', $settlementId);
		return $statement->executeQuery()->fetchAllAssociative();
	}
}

