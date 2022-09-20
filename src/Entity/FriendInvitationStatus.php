<?php

namespace App\Entity;

use OpenApi\Attributes as OA;

#[OA\Schema]
enum FriendInvitationStatus: int {
	public static function getActiveCases(): array {
		return array_filter(self::cases(), fn ($case) => $case->isActive());
	}

	case PENDING = 0;
	case SEEN = 1;
	case ACCEPTED = 2;
	case DECLINED = 3;

	public function isActive(): bool {
		return match ($this) {
			self::PENDING, self::SEEN => true,
			self::ACCEPTED, self::DECLINED => false,
		};
	}
}
