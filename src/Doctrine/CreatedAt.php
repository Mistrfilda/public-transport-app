<?php

declare(strict_types=1);

namespace App\Doctrine;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

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
