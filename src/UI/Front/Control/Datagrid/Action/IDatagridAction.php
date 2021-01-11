<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Action;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\FrontDatagrid;

interface IDatagridAction
{
	public function getDatagrid(): FrontDatagrid;

	public function getId(): string;

	public function getLabel(): string;

	public function getIcon(): ?string;

	public function getDestination(): string;

	/** @return DatagridActionParameter[] */
	public function getParameters(): array;

	/** @return array<string, mixed> */
	public function formatParametersForAction(IEntity $row): array;

	public function getColor(): string;

	public function getTemplateFile(): string;
}
