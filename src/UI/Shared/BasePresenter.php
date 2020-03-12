<?php

declare(strict_types=1);

namespace App\UI\Shared;

use App\UI\Shared\Modal\ModalRendererControl;
use App\UI\Shared\Modal\ModalRendererControlFactory;
use Nette\Application\UI\Presenter;
use Nette\Utils\IHtmlString;

abstract class BasePresenter extends Presenter
{
    /** @var string */
    public const DEFAULT_MODAL_COMPONENT_NAME = 'modalRendererControl';

    /** @var ModalRendererControlFactory */
    protected $modalRendererControlFactory;

    /** @var string|null */
    private $modalComponentName = null;

    public function injectModalRendererControlFactory(ModalRendererControlFactory $modalRendererControlFactory): void
    {
        $this->modalRendererControlFactory = $modalRendererControlFactory;
    }

    public function showModal(
        string $componentName = self::DEFAULT_MODAL_COMPONENT_NAME,
        ?string $heading = null,
        ?IHtmlString $content = null
    ): void {
        $modalComponent = $this->getComponent($componentName);
        if (! $modalComponent instanceof ModalRendererControl) {
            throw new LogicException(sprintf(
                'Component %s is not instance of %s',
                $componentName,
                ModalRendererControl::class
            ));
        }

        $modalComponent->setParameters($heading, $content);
        $this->modalComponentName = $componentName;

        $this->payload->showModal = true;
        $this->payload->modalId = $modalComponent->getModalId();
        $this->redrawControl('modalComponentSnippet');
    }

    public function getModalComponentName(): ?string
    {
        return $this->modalComponentName;
    }

    protected function createComponentModalRendererControl(): ModalRendererControl
    {
        return $this->modalRendererControlFactory->create();
    }
}
