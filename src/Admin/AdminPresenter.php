<?php

declare(strict_types=1);

namespace App\Admin;

use Nette\Application\UI\Presenter;

abstract class AdminPresenter extends Presenter
{
    public function startup(): void
    {
        parent::startup();
        if (! $this->user->isLoggedIn()) {
            $this->redirect('Login:default', ['backlink' => $this->storeRequest()]);
        }
    }

    /**
     * @return string[]
     */
    public function formatLayoutTemplateFiles(): array
    {
        return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
    }
}
