<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @template T
 */
class PaginationResult {
	/** @var array<int, T> */
	#[Serializer\Groups(["pagination_result"])]
	private array $rows;

	#[Serializer\Groups(["pagination_result"])]
	private int $total;

	/**
	 * @return array<int, T>
	 */
	public function getRows(): array {
		return $this->rows;
	}

	/**
	 * @param array<int, T> $rows
	 * @return void
	 */
	public function setRows(array $rows): void {
		$this->rows = $rows;
	}

	/**
	 * @return int
	 */
	public function getTotal(): int {
		return $this->total;
	}

	/**
	 * @param int $total
	 * @return void
	 */
	public function setTotal(int $total): void {
		$this->total = $total;
	}
}
