<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Control;

use App\UI\Front\Homepage\HomepagePresenter;
use Nette\Security\User;
use stdClass;

/**
 * @property User $user
 * @property string $baseUrl
 * @property string $basePath
 * @property array $flashes
 * @property StatisticControl $control
 * @property HomepagePresenter $presenter
 * @property array $statistics
 * @property stdClass $_l
 * @property stdClass $_g
 * @property stdClass $_b
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class StatisticControlTemplate
{
}
