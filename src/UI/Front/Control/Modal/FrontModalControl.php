<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Modal;

use App\UI\Front\Base\BaseControl;
use Nette\Utils\IHtmlString;
use Nette\Utils\Random;

class FrontModalControl extends BaseControl
{
	protected string $modalId;

	protected ?string $templateFile = null;

	private ?string $heading = null;

	private ?IHtmlString $content = null;

	/** @var mixed[] */
	private array $additionalParameters = [];

	public function __construct()
	{
		$this->modalId = 'modal-' . Random::generate(4, '0-9');
	}

	/**
	 * @param mixed[] $additionalParameneters
	 */
	public function setParameters(
		?string $heading,
		?IHtmlString $content,
		array $additionalParameneters = []
	): void {
		$this->heading = $heading;
		$this->content = $content;
		$this->additionalParameters = $additionalParameneters;
	}

	public function render(): void
	{
		$this->getTemplate()->modalId = $this->modalId;
		$this->getTemplate()->heading = $this->heading;
		$this->getTemplate()->content = $this->content;
		$this->getTemplate()->additionalParameters = $this->additionalParameters;
		$this->getTemplate()->originalTemplateFile = $this->getOriginalTemplateFile();

		$this->getTemplate()->setFile($this->getTemplateFile());
		$this->getTemplate()->render();
	}

	public function getModalId(): string
	{
		return $this->modalId;
	}

	public function setTemplateFile(string $templateFile): void
	{
		$this->templateFile = $templateFile;
	}

	protected function getOriginalTemplateFile(): string
	{
		return __DIR__ . '/frontModal.latte';
	}

	protected function getTemplateFile(): string
	{
		if ($this->templateFile === null) {
			return $this->getOriginalTemplateFile();
		}

		return $this->templateFile;
	}
}
