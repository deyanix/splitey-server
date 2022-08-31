<?php

namespace App\Service;

use App\Model\SettlementArrangementItem;
use App\Model\SettlementSummaryItem;

class SettlementArrangementOptimizer {
	/** @var SettlementSummaryItem[] */
	private array $summary;
	/** @var array  */
	private array $arrangement;

	/**
	 * @param SettlementSummaryItem[] $summary Settlement's summary items
	 */
	public function __construct(array $summary) {
		$this->summary = $summary;
		$this->arrangement = [];
	}

	private function getMinimalRecord(): ?SettlementSummaryItem {
		$result = $this->summary[0] ?? null;
		foreach ($this->summary as $item) {
			if ($item->getBalance() < $result->getBalance()) {
				$result = $item;
			}
		}
		return $result;
	}

	private function getMaximumRecord(): ?SettlementSummaryItem {
		$result = $this->summary[0] ?? null;
		foreach ($this->summary as $record) {
			if ($record->getBalance() > $result->getBalance()) {
				$result = $record;
			}
		}
		return $result;
	}

	public function optimize(): array {
		while (true) {
			$minRecord = $this->getMinimalRecord();
			$maxRecord = $this->getMaximumRecord();
			if ($minRecord === null || $maxRecord === null) {
				break;
			}

			$minValue = $minRecord->getBalance();
			$maxValue = $maxRecord->getBalance();
			if ($minValue === 0. && $maxValue === 0.) {
				break;
			}

			$min = min(-$minValue, $maxValue);
			$maxRecord->setBalance($maxValue - $min);
			$minRecord->setBalance($minValue + $min);

			$this->arrangement[] = new SettlementArrangementItem(
				$minRecord->getMemberId(),
				$maxRecord->getMemberId(),
				$min
			);
		}

		return $this->arrangement;
	}
}
