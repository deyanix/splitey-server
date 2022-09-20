<?php

namespace App\Model\Form;

use App\Entity\User;
use OpenApi\Attributes as OA;

class FriendInvitationData {
	#[OA\Property('recipientId', type: 'integer')]
	private User $recipient;

	public function getRecipient(): User {
		return $this->recipient;
	}

	public function setRecipient(User $recipient): void {
		$this->recipient = $recipient;
	}
}
