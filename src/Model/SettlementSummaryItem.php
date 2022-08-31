<?php

namespace App\Model;

class SettlementSummaryItem {
	private int $memberId;
	private float $balance;

	/**
	 * @param int $memberId
	 * @param float $balance
	 */
	public function __construct(int $memberId, float $balance) {
		$this->memberId = $memberId;
		$this->balance = $balance;
	}

	/**
	 * @return int
	 */
	public function getMemberId(): int {
		return $this->memberId;
	}

	/**
	 * @return float
	 */
	public function getBalance(): float {
		return $this->balance;
	}

	/**
	 * @param float $balance
	 */
	public function setBalance(float $balance): void {
		$this->balance = $balance;
	}
}
