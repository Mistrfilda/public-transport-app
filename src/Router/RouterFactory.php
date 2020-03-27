<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList();

        $adminRouter = new RouteList('Admin');
        $adminRouter->addRoute('admin/<presenter>/<action>[/<id>]', 'Dashboard:default');

        $frontRouter = new RouteList('Front');
        $frontRouter->addRoute('statistic/trip/<tripId>', 'Statistic:trip');
        $frontRouter->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');

        $router->add($adminRouter);
        $router->add($frontRouter);

        return $router;
    }
}
