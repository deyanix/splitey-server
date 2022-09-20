<?php

namespace App\Model;

class PaginationData {
	private int $page;
	private int $pageSize;

	public function getPage(): int {
		return $this->page;
	}

	public function setPage(int $page): void {
		$this->page = $page;
	}

	public function getPageSize(): int {
		return $this->pageSize;
	}

	public function setPageSize(int $pageSize): void {
		$this->pageSize = $pageSize;
	}
}
