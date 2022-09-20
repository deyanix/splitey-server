<?php

namespace App\Repository;

use App\Entity\Device;
use App\Entity\RefreshToken;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

class RefreshTokenRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, RefreshToken::class);
	}

	public function invalidateTokensByDevice(Device $device): void {
		$this->createQueryBuilder('rt')
			->update('App:RefreshToken', 'rt')
			->set('rt.invalidationDate', ':invalidationDate')
			->where('rt.device = :device')
			->setParameter('invalidationDate', new DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
			->setParameter('device', $device)
			->getQuery()
			->execute();
	}

	public function invalidateToken(RefreshToken $token): void {
		$this->createQueryBuilder('rt')
			->update('App:RefreshToken', 'rt')
			->set('rt.invalidationDate', ':invalidationDate')
			->where('rt.id = :id')
			->setParameter('invalidationDate', new DateTimeImmutable(), Types::DATETIME_IMMUTABLE)
			->setParameter('id', $token)
			->getQuery()
			->execute();
		$this->getEntityManager()->detach($token);
	}
}
