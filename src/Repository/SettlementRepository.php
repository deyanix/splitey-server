<?php

namespace App\Repository;

use App\Entity\ExternalFriend;
use App\Entity\Settlement;
use App\Entity\User;
use App\Model\PaginationResult;
use App\Model\SettlementArrangementItem;
use App\Model\SettlementSummaryItem;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

class SettlementRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Settlement::class);
	}

	public function findByUser(User $user, int $offset, int $length): PaginationResult {
		$rsm = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata(Settlement::class, 's');

		$rsmTotal = new Query\ResultSetMappingBuilder($this->getEntityManager());
		$rsmTotal->addScalarResult('total', 'total', 'integer');

		$query = $this->prepareNativeQuery('Settlement/Search', $rsm)
			->setParameter('user_id', $user->getId())
			->setParameter('offset', $offset)
			->setParameter('length', $length);

		$result = new PaginationResult();
		$result->setRows($query->getResult());
		$result->setTotal($query->setResultSetMapping($rsmTotal)->getResult()[0]['total'] ?? 0);
		return $result;
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

	public function isUserMember(int $id, User $user): bool {
		return $this->createQueryBuilder('s')
			->select('1')
			->innerJoin('s.members', 'sm', '')
			->where('s.id = :id')
			->andWhere('sm.user = :user')
			->setParameter('id', $id)
			->setParameter('user', $user)
			->setMaxResults(1)
			->getQuery()
			->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR) === 1;
	}

	public function isExternalFriendMember(int $id, ExternalFriend $friend): bool {
		return $this->createQueryBuilder('s')
				->select('1')
				->innerJoin('s.members', 'sm', '')
				->where('s.id = :id')
				->andWhere('sm.externalFriend = :friend')
				->setParameter('id', $id)
				->setParameter('friend', $friend)
				->setMaxResults(1)
				->getQuery()
				->getOneOrNullResult(AbstractQuery::HYDRATE_SINGLE_SCALAR) === 1;
	}

	/**
	 * @param int $settlementId Settlement id
	 * @return SettlementSummaryItem[] Settlement's summary items
	 * @throws \Doctrine\DBAL\Exception Database exception
	 */
	public function getSummary(int $settlementId): array {
		$statement = $this->prepareQuery('Settlement/Summary');
		$statement->bindValue('settlement_id', $settlementId);
		$result = $statement->executeQuery()->fetchAllAssociative();

		return array_map(fn ($row) => new SettlementSummaryItem($row['member_id'], $row['balance']), $result);
	}

	public function getArrangement(int $settlementId): array {
		$statement = $this->prepareQuery('Settlement/Arrangement');
		$statement->bindValue('settlement_id', $settlementId);
		$result =  $statement->executeQuery()->fetchAllAssociative();

		return array_map(fn ($row) => new SettlementArrangementItem($row['creditor_id'], $row['debtor_id'], $row['amount']), $result);
	}
}

