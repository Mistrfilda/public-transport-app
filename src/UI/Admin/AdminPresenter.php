<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\Admin\CurrentAppAdminGetter;
use App\UI\Admin\Control\Modal\ModalRendererControl;
use App\UI\Admin\Control\Modal\ModalRendererControlFactory;
use App\UI\Admin\Menu\AdminMenuBuilder;
use App\UI\Shared\BasePresenter;
use App\UI\Shared\LogicException;
use Nette\Utils\IHtmlString;

abstract class AdminPresenter extends BasePresenter
{
	/** @var string */
	public const DEFAULT_MODAL_COMPONENT_NAME = 'modalRendererControl';

	protected CurrentAppAdminGetter $currentAppAdminGetter;

	protected ModalRendererControlFactory $modalRendererControlFactory;

	private ?string $modalComponentName = null;

	public function injectModalRendererControlFactory(ModalRendererControlFactory $modalRendererControlFactory): void
	{
		$this->modalRendererControlFactory = $modalRendererControlFactory;
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
		if (! $modalComponent instanceof ModalRendererControl) {
			throw new LogicException(sprintf(
				'Component %s is not instance of %s',
				$componentName,
				ModalRendererControl::class
			));
		}

		$modalComponent->setParameters($heading, $content, $additionalParameters);

		if ($templateFile !== null) {
			$modalComponent->setTemplateFile($templateFile);
		}

		$this->modalComponentName = $componentName;

		$this->payload->showModal = true;
		$this->payload->modalId = $modalComponent->getModalId();
		$this->redrawControl('modalComponentSnippet');
	}

	public function getModalComponentName(): ?string
	{
		return $this->modalComponentName;
	}

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
		$this->template->menuItems = (new AdminMenuBuilder())->buildMenu();
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

	protected function createComponentModalRendererControl(): ModalRendererControl
	{
		return $this->modalRendererControlFactory->create();
	}
}
