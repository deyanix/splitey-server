<?php

namespace App\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigatorInterface;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonDeserializationVisitor;
use JMS\Serializer\JsonSerializationVisitor;
use ReflectionEnum;
use UnitEnum;

class EnumHandler implements SubscribingHandlerInterface {
	public static function getSubscribingMethods() {
		return [
			[
				'direction' => GraphNavigatorInterface::DIRECTION_SERIALIZATION,
				'type' => 'Enum',
				'format' => 'json',
				'method' => 'serializeEnum',
			],
			[
				'direction' => GraphNavigatorInterface::DIRECTION_DESERIALIZATION,
				'type' => 'Enum',
				'format' => 'json',
				'method' => 'deserializeEnum',
			],
		];
	}

	public function deserializeEnum(JsonDeserializationVisitor $visitor, $value, array $type, Context $context) {
		$enumName = $type['params'][0]['name'] ?? null;
		if ($enumName === null || !enum_exists($enumName, false)) {
			throw new \InvalidArgumentException('Enum name should be explicitly set for deserialization');
		}

		$reflection = new ReflectionEnum($enumName);
		return $reflection->getCase($value);
	}

	public function serializeEnum(JsonSerializationVisitor $visitor, $enum, array $type, Context $context) {
		if (!($enum instanceof UnitEnum)) {
			throw new \InvalidArgumentException('Value should be an enum');
		}

		return $enum->name;
	}
}
