<?php

declare(strict_types=1);

namespace App\UI\Shared;

use Nette\Application\BadRequestException;
use Nette\Application\UI\InvalidLinkException;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	/**
	 * @param string[] $links
	 * @throws InvalidLinkException
	 */
	public function isMenuLinkActive(array $links): bool
	{
		foreach ($links as $link) {
			if ($this->isLinkCurrent($link)) {
				return true;
			}
		}

		return false;
	}

	protected function processParameterIntId(): int
	{
		$id = $this->getParameter('id');
		if ($id === null || (int) $id === 0) {
			throw new BadRequestException('Missing parameter ID');
		}

		return (int) $id;
	}

	protected function processParameterStringId(string $parameterName = 'id'): string
	{
		$id = $this->getParameter($parameterName);
		if ($id === null || (string) $id === '') {
			throw new BadRequestException('Missing parameter ID');
		}

		return (string) $id;
	}
}
