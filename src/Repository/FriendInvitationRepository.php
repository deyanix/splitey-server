<?php

namespace App\Repository;

use App\Entity\FriendInvitation;
use App\Entity\FriendInvitationStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class FriendInvitationRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, FriendInvitation::class);
	}

	public function getActiveInvitations(User $recipient): array {
		return $this->createQueryBuilder('fi')
			->select('fi')
			->where('fi.recipient = :recipient')
			->andWhere('fi.status IN (:statuses)')
			->orderBy('fi.date', 'DESC')
			->setParameter('recipient', $recipient)
			->setParameter('statuses', FriendInvitationStatus::getActiveCases())
			->getQuery()
			->getResult();
	}

	public function getActiveInvitation(User $user1, User $user2): ?FriendInvitation {
		$qb = $this->createQueryBuilder('fi');
		return $qb
			->select('fi')
			->where(
				$qb->expr()->orX(
					$qb->expr()->andX(
						'fi.sender = :user1', 'fi.recipient = :user2'
					),
					$qb->expr()->andX(
						'fi.sender = :user2', 'fi.recipient = :user1'
					)
				)
			)
			->andWhere('fi.status IN (:statuses)')
			->orderBy('fi.date', 'DESC')
			->setParameter('user1', $user1)
			->setParameter('user2', $user2)
			->setParameter('statuses', FriendInvitationStatus::getActiveCases())
			->getQuery()
			->getOneOrNullResult();
	}

	public function getInvitation(User $sender, User $recipient): ?FriendInvitation {
		return $this->createQueryBuilder('fi')
			->select('fi')
			->where('fi.sender = :sender')
			->andWhere('fi.recipient = :recipient')
			->orderBy('fi.date', 'DESC')
			->setParameter('sender', $sender)
			->setParameter('recipient', $recipient)
			->getQuery()
			->getOneOrNullResult();
	}
}
