<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTable;
use App\Transport\Prague\StopLine\StopLine;
use App\UI\Admin\Prague\PragueDepartureTable\PragueDepartureTablePresenter;
use Nette\Security\User;
use stdClass;

/**
 * @property User $user
 * @property string $baseUrl
 * @property string $basePath
 * @property array $flashes
 * @property DepartureTableControl $control
 * @property PragueDepartureTablePresenter $presenter
 * @property StopLine[] $stopLines
 * @property DepartureTable $departureTable
 * @property bool $renderModal
 * @property stdClass $_l
 * @property stdClass $_g
 * @property stdClass $_b
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class DepartureTableControlLayoutTemplate
{
}
