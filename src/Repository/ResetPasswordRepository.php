<?php

namespace App\Repository;

use App\Entity\ResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ResetPasswordRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, ResetPassword::class);
	}
}
