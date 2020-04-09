<?php

declare(strict_types=1);

namespace App\Doctrine;

use DateTimeImmutable;

trait UpdatedAt
{
	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $updatedAt;

	public function getUpdatedAt(): DateTimeImmutable
	{
		return $this->updatedAt;
	}
}
