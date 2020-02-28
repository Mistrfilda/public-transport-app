<?php

declare(strict_types=1);

namespace App\Request\RabbitMQ;

use App\Request\Request;

abstract class BaseProducer
{
    /** @var MessageFactory */
    protected $messageFactory;

    public function injectMessageFactory(MessageFactory $messageFactory): void
    {
        $this->messageFactory = $messageFactory;
    }

    abstract public function publish(Request $request): void;
}
