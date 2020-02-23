<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\Admin\CurrentAppAdminGetter;
use Nette\Application\UI\Presenter;

abstract class AdminPresenter extends Presenter
{
    /** @var CurrentAppAdminGetter */
    protected $currentAppAdminGetter;

    public function injectCurrentAppAdminGetter(CurrentAppAdminGetter $currentAppAdminGetter): void
    {
        $this->currentAppAdminGetter = $currentAppAdminGetter;
    }

    public function startup(): void
    {
        parent::startup();
        if ($this->currentAppAdminGetter->isLoggedIn() === false) {
            $this->redirect('Login:default', ['backlink' => $this->storeRequest()]);
        }

        $this->template->appAdmin = $this->currentAppAdminGetter->getAppAdmin();
    }

    /**
     * @return string[]
     */
    public function formatLayoutTemplateFiles(): array
    {
        return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
    }

    public function handleLogout(): void
    {
        $this->currentAppAdminGetter->logout();
        $this->redirect('this');
    }
}
