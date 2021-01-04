<?php

declare(strict_types=1);

namespace App\UI\Front\Base;

use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;

class BaseControlTemplate extends Template
{
	public Presenter $presenter;

	public BaseControl $control;
}
