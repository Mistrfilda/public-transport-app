<?php

declare(strict_types = 1);


namespace App\UI\Front\Prague\PragueTransportRestriction\Control\Modal;


use Ramsey\Uuid\UuidInterface;


interface PragueRestrictionModalControlFactory
{
	public function create(): PragueRestrictionModalControl;
}