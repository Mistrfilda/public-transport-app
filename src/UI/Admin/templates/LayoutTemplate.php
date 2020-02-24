<?php

declare(strict_types=1);

namespace App\UI\Admin\templates;

use App\Admin\AppAdmin;
use App\UI\Admin\Dashboard\DashboardPresenter;
use App\UI\Admin\Menu\MenuGroup;
use Nette\Security\User;

class LayoutTemplate
{
    /** @var User */
    public $user;

    /** @var string */
    public $baseUrl;

    /** @var string */
    public $basePath;

    /** @var mixed[] */
    public $flashes;

    /** @var DashboardPresenter */
    public $control;

    /** @var DashboardPresenter */
    public $presenter;

    /** @var AppAdmin */
    public $appAdmin;

    /** @var MenuGroup[] */
    public $menuItems;
}
