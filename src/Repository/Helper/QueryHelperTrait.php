<?php

namespace App\Repository\Helper;

use Doctrine\DBAL\Statement;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;

trait QueryHelperTrait {
	protected function getQuerySql(string $queryName): ?string {
		return file_get_contents(__DIR__ . "/../../../queries/$queryName.sql");
	}

	protected function prepareQuery(string $queryName): Statement {
		return $this->getEntityManager()->getConnection()->prepare($this->getQuerySql($queryName));
	}

	protected function prepareNativeQuery(string $queryName, ResultSetMapping $rsm): NativeQuery {
		return $this->getEntityManager()->createNativeQuery($this->getQuerySql($queryName), $rsm);
	}
}
