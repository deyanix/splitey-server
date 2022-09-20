<?php

namespace App\Repository;

use App\Entity\FriendInvitation;
use App\Entity\FriendInvitationStatus;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FriendInvitationRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, FriendInvitation::class);
	}

	public function seeActiveInvitationsFor(User $recipient): void {
		$this->createQueryBuilder('fi')
			->update()
			->set('fi.seen', true)
			->where('fi.recipient = :recipient')
			->andWhere('fi.active = 1')
			->setParameter('recipient', $recipient)
			->getQuery()
			->execute();
	}

	public function getActiveInvitation(int $id): ?FriendInvitation {
		return $this->findOneBy(['id' => $id, 'active' => true]);
	}

	public function getSentInvitationsFor(User $sender): array {
		return $this->createQueryBuilder('fi')
			->select('fi')
			->where('fi.sender = :sender')
			->andWhere('fi.active = 1')
			->orderBy('fi.date', 'DESC')
			->setParameter('sender', $sender)
			->getQuery()
			->getResult();
	}

	public function getActiveInvitationsFor(User $recipient): array {
		return $this->createQueryBuilder('fi')
			->select('fi')
			->where('fi.recipient = :recipient')
			->andWhere('fi.active = 1')
			->orderBy('fi.date', 'DESC')
			->setParameter('recipient', $recipient)
			->getQuery()
			->getResult();
	}

	public function getActiveInvitationFor(User $user1, User $user2): ?FriendInvitation {
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
			->andWhere('fi.active = 1')
			->orderBy('fi.date', 'DESC')
			->setParameter('user1', $user1)
			->setParameter('user2', $user2)
			->getQuery()
			->getOneOrNullResult();
	}
}
