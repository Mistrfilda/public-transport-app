<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Action;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\TailwindConstant;

class DatagridAction implements IDatagridAction
{
	private const TEMPLATE_FILE = __DIR__ . '/templates/datagridAction.latte';

	private FrontDatagrid $datagrid;

	private string $id;

	private string $label;

	private ?string $icon;

	private string $color;

	private string $destination;

	/** @var DatagridActionParameter[] */
	private array $parameters;

	/**
	 * DatagridAction constructor.
	 * @param DatagridActionParameter[] $parameters
	 */
	public function __construct(
		FrontDatagrid $datagrid,
		string $id,
		string $label,
		string $destination,
		array $parameters,
		?string $icon = null,
		string $color = TailwindConstant::BLUE
	) {
		$this->id = $id;
		$this->label = $label;
		$this->icon = $icon;
		$this->destination = $destination;
		$this->parameters = $parameters;
		$this->color = $color;
		$this->datagrid = $datagrid;
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getIcon(): ?string
	{
		return $this->icon;
	}

	public function getDestination(): string
	{
		return $this->destination;
	}

	/**
	 * @return DatagridActionParameter[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function getColor(): string
	{
		return $this->color;
	}

	public function getTemplateFile(): string
	{
		return self::TEMPLATE_FILE;
	}

	public function getDatagrid(): FrontDatagrid
	{
		return $this->datagrid;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function formatParametersForAction(IEntity $row): array
	{
		$formatedParameters = [];
		foreach ($this->parameters as $parameter) {
			$formatedParameters[$parameter->getParameter()] =
				$this->datagrid->getDatasource()->getValueForKey(
					$parameter->getReferencedColumn(),
					$row
				);
		}

		return $formatedParameters;
	}
}
