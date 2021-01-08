<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Pagination;

class Pagination
{
	private int $limit;

	private int $offset;

	/**
	 * @var PaginationItem[]
	 */
	private array $paginationItems;

	/**
	 * @param PaginationItem[] $paginationItems
	 */
	public function __construct(
		int $limit,
		int $offset,
		array $paginationItems
	) {
		$this->limit = $limit;
		$this->offset = $offset;
		$this->paginationItems = $paginationItems;
	}

	public function getLimit(): int
	{
		return $this->limit;
	}

	public function getOffset(): int
	{
		return $this->offset;
	}

	/**
	 * @return PaginationItem[]
	 */
	public function getPaginationItems(): array
	{
		return $this->paginationItems;
	}
}
