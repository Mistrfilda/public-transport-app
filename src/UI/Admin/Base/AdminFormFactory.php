<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

class AdminFormFactory
{
    public function create(?string $mappedClass = null): AdminForm
    {
        $form = new AdminForm();
        if ($mappedClass !== null) {
            $form->setMappedType($mappedClass);
        }
        $form->setRenderer(new BootstrapFormRenderer());

        return $form;
    }
}
