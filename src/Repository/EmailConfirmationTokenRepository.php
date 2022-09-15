<?php

namespace App\Repository;

use App\Entity\EmailConfirmationToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EmailConfirmationTokenRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, EmailConfirmationToken::class);
	}
}
