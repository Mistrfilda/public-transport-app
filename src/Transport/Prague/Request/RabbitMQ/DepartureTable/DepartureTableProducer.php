<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\DepartureTable;

use App\Request\RabbitMQ\BaseProducer;
use App\Request\Request;
use Gamee\RabbitMQ\Producer\Producer;

class DepartureTableProducer extends BaseProducer
{
	/** @var Producer */
	private $producer;

	public function __construct(Producer $producer)
	{
		$this->producer = $producer;
	}

	public function publish(Request $request): void
	{
		$this->producer->publish($this->messageFactory->getMessage($request));
	}
}
