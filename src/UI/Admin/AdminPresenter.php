<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\Admin\CurrentAppAdminGetter;
use App\UI\Admin\Menu\MenuBuilder;
use Nette\Application\UI\InvalidLinkException;
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
        $this->template->menuItems = (new MenuBuilder())->buildMenu();
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

    /**
     * @param string[] $links
     * @throws InvalidLinkException
     */
    public function isMenuLinkActive(array $links): bool
    {
        foreach ($links as $link) {
            if ($this->isLinkCurrent($link)) {
                return true;
            }
        }

        return false;
    }
}
