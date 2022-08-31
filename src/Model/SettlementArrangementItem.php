<?php

namespace App\Model;

class SettlementArrangementItem {
	private int $debtorId;
	private int $creditorId;
	private float $amount;

	/**
	 * @param int $debtorId
	 * @param int $creditorId
	 * @param float $amount
	 */
	public function __construct(int $debtorId, int $creditorId, float $amount) {
		$this->debtorId = $debtorId;
		$this->creditorId = $creditorId;
		$this->amount = $amount;
	}

	/**
	 * @return int
	 */
	public function getDebtorId(): int {
		return $this->debtorId;
	}

	/**
	 * @return int
	 */
	public function getCreditorId(): int {
		return $this->creditorId;
	}

	/**
	 * @return float
	 */
	public function getAmount(): float {
		return $this->amount;
	}
}
