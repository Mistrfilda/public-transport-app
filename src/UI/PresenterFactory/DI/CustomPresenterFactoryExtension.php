<?php

declare(strict_types=1);

namespace App\UI\PresenterFactory\DI;

use App\UI\PresenterFactory\CustomPresenterFactory;
use Nette\Application\IPresenterFactory;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\ServiceDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class CustomPresenterFactoryExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'presenterDir' => Expect::string(),
			'customMapping' => Expect::arrayOf(Expect::string()),
		])->castTo('array');
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var ServiceDefinition $nettePresenterFactory */
		$nettePresenterFactory = $builder->getDefinitionByType(IPresenterFactory::class);
		$arguments = $nettePresenterFactory->getFactory()->arguments;

		/** @var array<string, string> $config */
		$config = $this->config;

		$parameters = [
			'appDir' => $config['presenterDir'],
			'customMapping' => $config['customMapping'],
			'factory' => $arguments[0],
		];
		$nettePresenterFactory->setFactory(CustomPresenterFactory::class, $parameters);
	}
}
