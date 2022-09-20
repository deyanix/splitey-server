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

	public function hasUserFriend(User $user1, User $user2): bool {
		$qb = $this->createQueryBuilder('f');
		return $qb
			->select('1')
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
			->getOneOrNullResult() === 1;
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
