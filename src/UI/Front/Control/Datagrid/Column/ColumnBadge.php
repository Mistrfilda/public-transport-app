<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use Nette\Utils\Callback;


class ColumnBadge extends ColumnText
{
	public const TEMPLATE_FILE = __DIR__ . '/templates/columnBadge.latte';

	protected string $color;

	/** @var callable|null */
	protected $colorCallback;

	public function __construct(
		FrontDatagrid $datagrid,
		string $label,
		string $column,
		string $color,
		?callable $getterMethod = null,
		?callable $colorCallback = null
	) {
		parent::__construct($datagrid, $label, $column, $getterMethod);
		$this->color = $color;
		$this->colorCallback = $colorCallback;
	}

	public function getColor(): string
	{
		return $this->color;
	}

	public function getTemplate(): string
	{
		return self::TEMPLATE_FILE;
	}

	public function getColorCallback(): ?callable
	{
		return $this->colorCallback;
	}

	public function getColorClasses(IEntity $entity): string
	{
		$colorTemplate = 'bg-%s-100 text-%s-600';
		if ($this->colorCallback !== null) {
			$callback = Callback::check($this->getColorCallback());
			$color = $callback($entity);
			return sprintf($colorTemplate, $color, $color);
		}

		return sprintf($colorTemplate, $this->color, $this->color);
	}
}
