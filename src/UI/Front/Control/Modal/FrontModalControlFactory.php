<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Modal;

interface FrontModalControlFactory
{
	public function create(): FrontModalControl;
}
