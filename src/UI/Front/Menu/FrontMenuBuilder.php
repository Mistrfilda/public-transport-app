<?php

declare(strict_types = 1);


namespace App\UI\Front\Menu;


use App\UI\Shared\Menu\MenuItem;


class FrontMenuBuilder
{
	/**
	 * @return MenuItem[]
	 */
	public function buildMenu(): array
	{
		return [
			new MenuItem('Homepage', 'default', '', 'Hlavní stránka'),
			new MenuItem('PragueDepartureTableList', 'default', '', 'Odjezdové tabule'),
			new MenuItem('Map', 'default', '', 'Mapa vozidel'),
			new MenuItem('PragueTransportRestriction', 'default', '', 'Mimořádnosti'),
			new MenuItem('PragueParkingLot', 'default', '', 'Parkoviště'),
			new MenuItem('Statistic', 'default', '', 'Statistiky'),
		];
	}
}