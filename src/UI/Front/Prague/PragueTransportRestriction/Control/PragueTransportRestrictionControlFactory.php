<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control;

interface PragueTransportRestrictionControlFactory
{
	public function create(): PragueTransportRestrictionControl;
}
