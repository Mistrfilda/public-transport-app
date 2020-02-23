<?php

declare(strict_types=1);

namespace App\UI\Admin\Login;

use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\Login\Form\LoginFormFactory;
use Nette\Application\UI\Presenter;

class LoginPresenter extends Presenter
{
    /** @var LoginFormFactory */
    private $loginFormFactory;

    public function __construct(LoginFormFactory $loginFormFactory)
    {
        parent::__construct();
        $this->loginFormFactory = $loginFormFactory;
    }

    protected function createComponentLoginForm(): AdminForm
    {
        $onSuccess = function (): void {
            $this->restoreRequest('backlink');
            $this->redirect('Dashboard:default');
        };

        return $this->loginFormFactory->create($onSuccess);
    }
}
