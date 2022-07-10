<?php

namespace App\Repository;

use App\Entity\Settlement;
use App\Entity\SettlementMember;
use App\Entity\User;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SettlementRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Settlement::class);
	}

	private function createQueryByUser(User $user): QueryBuilder {
		return $this->createQueryBuilder('s')
			->innerJoin('s.members', 'sm', '')
			->where('sm.user = :user')
			->setParameter('user', $user);
	}

	public function findByUser(User $user, int $offset, int $length): array {
		$rsm = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata(Settlement::class, 's');

		$rsm1 = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsm1->addScalarResult('total', 'total', 'integer');

		$query = $this->getEntityManager()->createNativeQuery("
SELECT s.id, s.name, COUNT(*) OVER() AS total 
FROM settlement s 
    INNER JOIN settlement_member sm ON s.id = sm.settlement_id 
WHERE sm.user_id = :user_id 
LIMIT :offset,:length 
", $rsm)
			->setParameter('user_id', $user->getId())
			->setParameter('offset', $offset)
			->setParameter('length', $length);

		$rows = $query->getResult();
		$total = $query->setResultSetMapping($rsm1)->getResult();

		return [
			'rows' => $rows,
			'total' => $total[0]['total']
		];
//		return $this->createQueryByUser($user)
//			->addSelect('COUNT(s) total')
//			->setFirstResult($offset)
//			->setMaxResults($length)
//			->getQuery()
//			->getResult();
	}

	public function countByUser(User $user): int {
		return $this->createQueryByUser($user)
			->select('COUNT(s) total')
			->getQuery()
			->getSingleScalarResult();
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

	public function getSummary(int $settlementId): array {
		$statement = $this->prepareQuery('Settlement/Summary');
		$statement->bindValue('settlement_id', $settlementId);
		return $statement->executeQuery()->fetchAllAssociative();
	}
}

