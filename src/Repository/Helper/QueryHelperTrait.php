<?php

namespace App\Repository\Helper;

use Doctrine\DBAL\Statement;

trait QueryHelperTrait {
	protected function prepareQuery($queryName): Statement {
		$sql = file_get_contents(__DIR__ . "/../../../queries/$queryName.sql");
		return $this->getEntityManager()->getConnection()->prepare($sql);
	}
}
