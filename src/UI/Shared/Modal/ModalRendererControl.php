<?php

declare(strict_types=1);

namespace App\UI\Shared\Modal;

use Nette\Application\UI\Control;
use Nette\Utils\IHtmlString;
use Nette\Utils\Random;

class ModalRendererControl extends Control
{
	protected string $modalId;

	protected ?string $templateFile = null;

	private ?string $heading = null;

	private ?IHtmlString $content = null;

	public function __construct()
	{
		$this->modalId = 'modal-' . Random::generate(4, '0-9');
	}

	public function setParameters(?string $heading, ?IHtmlString $content): void
	{
		$this->heading = $heading;
		$this->content = $content;
	}

	public function render(): void
	{
		$this->getTemplate()->modalId = $this->modalId;
		$this->getTemplate()->heading = $this->heading;
		$this->getTemplate()->content = $this->content;
		$this->getTemplate()->originalTemplateFile = $this->getOriginalTemplateFile();

		$this->getTemplate()->setFile($this->getTemplateFile());
		$this->getTemplate()->render();
	}

	public function getModalId(): string
	{
		return $this->modalId;
	}

	protected function getOriginalTemplateFile(): string
	{
		return __DIR__ . '/modal.latte';
	}

	protected function getTemplateFile(): string
	{
		if ($this->templateFile === null) {
			return $this->getOriginalTemplateFile();
		}

		return $this->templateFile;
	}
}
