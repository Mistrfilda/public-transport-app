<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\ORM\Mapping as ORM;

trait Identifier
{
	/**
	 * @var int
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue()
	 */
	private $id;

	public function getId(): int
	{
		return $this->id;
	}
}
