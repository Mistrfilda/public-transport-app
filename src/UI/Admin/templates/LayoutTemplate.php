<?php

declare(strict_types=1);

namespace App\UI\Admin\templates;

use App\Admin\AppAdmin;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Dashboard\DashboardPresenter;
use App\UI\Admin\Menu\MenuGroup;
use Nette\Security\User;
use stdClass;

/**
 * @property User $user
 * @property string $baseUrl
 * @property string $basePath
 * @property mixed $flashes
 * @property DashboardPresenter $control
 * @property AdminPresenter $presenter
 * @property AppAdmin $appAdmin
 * @property MenuGroup[] $menuItems
 * @property stdClass $_l
 * @property stdClass $_g
 * @property stdClass $_b
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 * @method string|null getModalComponentName()
 */
class LayoutTemplate
{
}
