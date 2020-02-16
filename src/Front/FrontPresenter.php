<?php

declare(strict_types=1);

namespace App\Front;

use Nette\Application\UI\Presenter;

abstract class FrontPresenter extends Presenter
{
    /**
     * @return string[]
     */
    public function formatLayoutTemplateFiles(): array
    {
        return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
    }
}
