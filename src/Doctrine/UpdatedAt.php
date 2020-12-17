<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

trait UpdatedAt
{
	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $updatedAt;

	public function getUpdatedAt(): DateTimeImmutable
	{
		return $this->updatedAt;
	}
}
