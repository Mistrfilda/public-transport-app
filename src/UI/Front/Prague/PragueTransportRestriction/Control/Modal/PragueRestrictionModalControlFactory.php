<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control\Modal;

interface PragueRestrictionModalControlFactory
{
	public function create(): PragueRestrictionModalControl;
}
