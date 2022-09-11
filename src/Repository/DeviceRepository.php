<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DeviceRepository extends ServiceEntityRepository {
	public function __construct(ManagerRegistry $registry) {
		parent::__construct($registry, Device::class);
	}
}
