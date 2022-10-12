<?php

namespace App\Model\Form;

use App\Entity\ExternalFriend;
use App\Entity\User;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SettlementMemberData {
	#[OA\Property('userId', type: 'integer')]
	private ?User $user = null;

	#[OA\Property('externalFriendId', type: 'integer')]
	private ?ExternalFriend $externalFriend = null;

	public function getUser(): ?User {
		return $this->user;
	}

	public function setUser(?User $user): void {
		$this->user = $user;
	}

	public function getExternalFriend(): ?ExternalFriend {
		return $this->externalFriend;
	}

	public function setExternalFriend(?ExternalFriend $externalFriend): void {
		$this->externalFriend = $externalFriend;
	}

	#[Assert\Callback]
	public function validate(ExecutionContextInterface $context): void {
		$fields = 0;
		$fields += $this->getUser() === null;
		$fields += $this->getExternalFriend() === null;

		if ($fields !== 1) {
			$context->buildViolation('Required only one of two fields.')
				->atPath('user')
				->addViolation();
		}
	}
}
