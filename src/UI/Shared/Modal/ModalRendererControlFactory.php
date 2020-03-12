<?php

declare(strict_types=1);

namespace App\UI\Shared\Modal;

interface ModalRendererControlFactory
{
    public function create(): ModalRendererControl;
}
