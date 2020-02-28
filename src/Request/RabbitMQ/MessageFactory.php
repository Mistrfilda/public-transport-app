<?php

declare(strict_types=1);

namespace App\Request\RabbitMQ;

use App\Request\Request;
use App\Request\RequestException;
use App\Request\RequestType;
use Nette\Utils\Json;

class MessageFactory
{
    public function getMessage(Request $request): string
    {
        switch ($request->getType()) {
            case RequestType::PRAGUE_DEPARTURE_TABLE:
                return Json::encode([
                    'requestId' => $request->getId(),
                    'departureTableId' => $request->getPragueDepartureTable()->getId(),
                    'dateTimestamp' => $request->getCreatedAt()->getTimestamp(),
                ]);
            case RequestType::PRAGUE_VEHICLE_POSITION:
                return Json::encode(
                    [
                        'requestId' => $request->getId(),
                        'dateTimestamp' => $request->getCreatedAt()->getTimestamp(),
                    ]
                );
        }

        throw new RequestException(sprintf('Invalid request type %s', $request->getType()));
    }
}
