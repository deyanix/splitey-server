<?php

namespace App\Model\Form;

use App\Entity\User;
use OpenApi\Attributes as OA;

class DeleteFriendData {
	#[OA\Property('userId', type: 'integer')]
	private User $user;

	public function getUser(): User {
		return $this->user;
	}

	public function setUser(User $user): void {
		$this->user = $user;
	}
}
