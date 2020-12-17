<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

trait CreatedAt
{
	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $createdAt;

	public function getCreatedAt(): DateTimeImmutable
	{
		return $this->createdAt;
	}
}
