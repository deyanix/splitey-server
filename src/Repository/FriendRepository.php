<?php

namespace App\Repository;

use App\Entity\Friend;
use App\Entity\User;
use App\Model\CommonFriend;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

class FriendRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Friend::class);
	}

	public function searchUsers(string $name, int $currentUserId): array {
		$rsm = new ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata(User::class, 'u');

		$rsmRelation = new ResultSetMappingBuilder($this->getEntityManager());
		$rsmRelation->addScalarResult('is_friend', 'isFriend', 'boolean');
		$rsmRelation->addScalarResult('received_invitation', 'receivedInvitationId', 'integer');
		$rsmRelation->addScalarResult('sent_invitation', 'sentInvitationId', 'integer');

		$statement = $this->prepareNativeQuery('Friend/SearchUsers', $rsm);
		$statement->setParameter('name', "%$name%");
		$statement->setParameter('currentUserId', $currentUserId);

		return array_map(
			fn ($row) => array_combine(['user', 'relation'], $row),
			array_map(null,
				$statement->getResult(),
				$statement->setResultSetMapping($rsmRelation)->getResult()
			)
		);
	}

	public function getUserFriend(User $user1, User $user2): ?Friend {
		$qb = $this->createQueryBuilder('f');
		return $qb
			->select('f')
			->where(
				$qb->expr()->orX(
					$qb->expr()->andX(
						'f.user1 = :user1', 'f.user2 = :user2'
					),
					$qb->expr()->andX(
						'f.user1 = :user2', 'f.user2 = :user1'
					)
				)
			)
			->setParameter('user1', $user1)
			->setParameter('user2', $user2)
			->getQuery()
			->getOneOrNullResult();
	}

	public function getUserFriends(User $user): array {
		$statement = $this->prepareQuery('Friend/Search');
		$statement->bindValue('user_id', $user->getId());
		$result = $statement->executeQuery()->fetchAllAssociative();

		return array_map(function ($row) {
			$friend = new CommonFriend();
			$friend->setUserId($row['user_id']);
			$friend->setExternalFriendId($row['external_friend_id']);
			$friend->setFirstName($row['first_name']);
			$friend->setLastName($row['last_name']);
			$friend->setUsername($row['username']);
			return $friend;
		}, $result);
	}
}
