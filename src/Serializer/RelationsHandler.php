<?php

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JMS\Serializer\Context;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;

final class RelationsHandler {
	private EntityManagerInterface $entityManager;

	public function __construct(EntityManagerInterface $entityManager) {
		$this->entityManager = $entityManager;
	}

	protected function getSingleEntityRelation($relation) {
		$metadata = $this->entityManager->getClassMetadata(get_class($relation));

		$ids = $metadata->getIdentifierValues($relation);
		if (!$metadata->isIdentifierComposite) {
			$ids = array_shift($ids);
		}

		return $ids;
	}

	/**
	 * @throws ORMException
	 */
	public function deserializeRelation(JsonDeserializationVisitor $visitor, $relation, array $type, Context $context) {
		$className = $type['params'][0]['name'] ?? null;

		if ($className === null || !class_exists($className, false)) {
			throw new \InvalidArgumentException('Class name should be explicitly set for deserialization');
		}

		$metadata = $this->entityManager->getClassMetadata($className);

		if (!is_array($relation)) {
			return $this->entityManager->getReference($className, $relation);
		}

		$single = false;
		if ($metadata->isIdentifierComposite) {
			$single = true;
			foreach ($metadata->getIdentifierFieldNames() as $idName) {
				$single = $single && array_key_exists($idName, $relation);
			}
		}

		if ($single) {
			return $this->entityManager->getReference($className, $relation);
		}

		$objects = [];
		foreach ($relation as $idSet) {
			$objects[] = $this->entityManager->getReference($className, $idSet);
		}

		return $objects;
	}

	public function serializeRelation(JsonSerializationVisitor $visitor, $relation, array $type, Context $context) {
		if ($relation instanceof \Traversable) {
			$relation = iterator_to_array($relation);
		}

		if (is_array($relation)) {
			return array_map([$this, 'getSingleEntityRelation'], $relation);
		}

		return $this->getSingleEntityRelation($relation);
	}
}
