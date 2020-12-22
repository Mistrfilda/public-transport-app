<?php

declare(strict_types=1);

namespace App\UI\Admin\Control\Modal;

interface ModalRendererControlFactory
{
	public function create(): ModalRendererControl;
}
