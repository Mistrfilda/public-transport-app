<?php

declare(strict_types=1);

namespace App\UI\Admin\Login\Form;

use App\Admin\CurrentAppAdminGetter;
use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\Base\AdminFormFactory;

class LoginFormFactory
{
    /** @var AdminFormFactory */
    private $adminFormFactory;

    /** @var CurrentAppAdminGetter */
    private $currentAppAdminGetter;

    public function __construct(
        AdminFormFactory $adminFormFactory,
        CurrentAppAdminGetter $currentAppAdminGetter
    ) {
        $this->adminFormFactory = $adminFormFactory;
        $this->currentAppAdminGetter = $currentAppAdminGetter;
    }

    public function create(callable $onSuccess): AdminForm
    {
        $form = $this->adminFormFactory->create(LoginFormDTO::class);

        $form->addText('username', 'Username')
            ->setRequired();

        $form->addPassword('password', 'Password')
            ->setRequired();

        $form->addSubmit('submit', 'Submit');

        $form->onSuccess[] = function (AdminForm $form, LoginFormDTO $values) use ($onSuccess): void {
            $this->currentAppAdminGetter->login($values->username, $values->password);
            $onSuccess();
        };

        return $form;
    }
}
