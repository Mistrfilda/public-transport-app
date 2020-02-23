<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard\templates;

use App\Admin\AppAdmin;
use App\UI\Admin\Dashboard\DashboardPresenter;
use Nette\Security\User;

class DashboardDefaultTemplate
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
}
