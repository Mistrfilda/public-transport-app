<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

use Nette\Application\UI\Form;

class AdminForm extends Form
{
	/** @var bool */
	private $isAjax = false;

	public function ajax(): void
	{
		$this->isAjax = true;
	}

	public function isAjax(): bool
	{
		return $this->isAjax;
	}
}
