<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class UserRepository extends ServiceEntityRepository {
	private LoggerInterface $logger;

	public function __construct(ManagerRegistry $registry, LoggerInterface $logger) {
		parent::__construct($registry, User::class);
		$this->logger = $logger;
	}

	public function findByUsernameOrEmail(string $value): ?User {
		try {
			return $this->createQueryBuilder('u')
				->where('u.disabled = :disabled')
				->andWhere('u.email = :value OR u.username = :value')
				->setParameter('disabled', false)
				->setParameter('value', $value)
				->setMaxResults(1)
				->getQuery()
				->getSingleResult();
		} catch (NoResultException|NonUniqueResultException) {
			$this->logger->info('No user');
		}
		return null;
	}
}
