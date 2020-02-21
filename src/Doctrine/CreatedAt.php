<?php

namespace App\Doctrine;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

trait CreatedAt
{
    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
