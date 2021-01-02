<?php

declare(strict_types=1);

namespace App\UI\Front;

use App\UI\Front\Control\Modal\FrontModalControl;
use App\UI\Front\Control\Modal\FrontModalControlFactory;
use App\UI\Front\Menu\FrontMenuBuilder;
use App\UI\Shared\BasePresenter;
use App\UI\Shared\LogicException;
use Nette\Utils\IHtmlString;

abstract class FrontPresenter extends BasePresenter
{
	/** @var string */
	public const DEFAULT_MODAL_COMPONENT_NAME = 'modalRendererControl';

	protected FrontModalControlFactory $frontModalControlFactory;

	private ?string $modalComponentName = null;

	public function injectModalRendererControlFactory(
		FrontModalControlFactory $frontModalControlFactory
	): void {
		$this->frontModalControlFactory = $frontModalControlFactory;
	}

	/**
	 * @param mixed[] $additionalParameters
	 * @throws LogicException
	 */
	public function showModal(
		string $componentName = self::DEFAULT_MODAL_COMPONENT_NAME,
		?string $heading = null,
		?IHtmlString $content = null,
		array $additionalParameters = [],
		?string $templateFile = null
	): void {
		$modalComponent = $this->getComponent($componentName);
		if (! $modalComponent instanceof FrontModalControl) {
			throw new LogicException(sprintf(
				'Component %s is not instance of %s',
				$componentName,
				FrontModalControl::class
			));
		}

		$modalComponent->setParameters(
			$heading,
			$content,
			$additionalParameters
		);

		if ($templateFile !== null) {
			$modalComponent->setTemplateFile($templateFile);
		}

		$this->modalComponentName = $componentName;

		$this->payload->showModal = true;
		$this->payload->modalId = $modalComponent->getModalId();
		$this->redrawControl('modalComponentSnippet');
	}

	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles(): array
	{
		return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
	}

	public function beforeRender(): void
	{
		$this->template->menuItems = (new FrontMenuBuilder())->buildMenu();
		parent::beforeRender();
	}

	public function getModalComponentName(): ?string
	{
		return $this->modalComponentName;
	}

	protected function createComponentModalRendererControl(): FrontModalControl
	{
		return $this->frontModalControlFactory->create();
	}
}
