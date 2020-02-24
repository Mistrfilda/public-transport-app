<?php

declare(strict_types=1);

namespace App\UI\Admin\Menu;

class MenuBuilder
{
    /**
     * @return MenuGroup[]
     */
    public function buildMenu(): array
    {
        return [
            new MenuGroup('Dashboard', false, [
                new MenuItem('Dashboard', 'default', 'fas fa-fw fa-tachometer-alt', 'Dashboard'),
            ]),
            new MenuGroup('Prague', true, [
                new MenuItem('', '', 'fas fa-fw fa-cog', 'Stops', [
                    new MenuItem('PragueStop', 'default', '', 'List'),
                    new MenuItem('PragueStop', 'map', '', 'Map'),
                ]),
                new MenuItem('PragueDepartureTable', 'default', 'fas fa-fw fa-cog', 'Departure tables'),
            ]),
        ];
    }
}
