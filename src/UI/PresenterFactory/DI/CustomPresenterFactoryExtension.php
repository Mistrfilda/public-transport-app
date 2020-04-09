<?php

declare(strict_types=1);

namespace App\UI\PresenterFactory\DI;

use App\UI\PresenterFactory\CustomPresenterFactory;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;

class CustomPresenterFactoryExtension extends CompilerExtension
{
	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $nettePresenterFactory */
		$nettePresenterFactory = $builder->getDefinitionByType(IPresenterFactory::class);
		$arguments = $nettePresenterFactory->getFactory()->arguments;
		$nettePresenterFactory->setFactory(CustomPresenterFactory::class, $arguments);
	}
}
