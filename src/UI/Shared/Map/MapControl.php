<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;

class MapControl extends Control
{
    /** @var MapObject[] */
    private $mapObjects;

    /** @var string */
    private $mapApiKey;

    /**
     * @param MapObject[] $mapObjects
     */
    public function __construct(string $mapApiKey, array $mapObjects)
    {
        $this->mapObjects = $mapObjects;
        $this->mapApiKey = $mapApiKey;
    }

    public function render(): void
    {
        $this->getTemplate()->mapApiKey = $this->mapApiKey;
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    public function handleGetMapObjects(): void
    {
        $response = new JsonResponse($this->mapObjects);
        $this->getPresenter()->sendResponse($response);
    }
}
