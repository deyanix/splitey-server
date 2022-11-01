<?php

namespace App\Repository;

use App\Entity\User;
use App\Repository\Helper\QueryHelperTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class UserRepository extends ServiceEntityRepository {
	use QueryHelperTrait;

	private LoggerInterface $logger;

	public function __construct(ManagerRegistry $registry, LoggerInterface $logger) {
		parent::__construct($registry, User::class);
		$this->logger = $logger;
	}

	public function searchUsers(string $name, int $currentUserId): array {
		$rsm = new ResultSetMappingBuilder($this->getEntityManager());
		$rsm->addRootEntityFromClassMetadata(User::class, 'u');

		$statement = $this->prepareNativeQuery('User/Search', $rsm);
		$statement->setParameter('name', "%$name%");
		$statement->setParameter('currentUserId', $currentUserId);

		return $statement->getResult();
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
