<?php

declare(strict_types=1);

namespace App\UI\Webpack\DI;

use App\UI\Webpack\WebpackAssetsFactory;
use App\UI\Webpack\WebpackEncoreMacro;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\FactoryDefinition;
use Nette\Schema\Expect;
use Nette\Schema\Schema;

class WebpackAssetExtension extends CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'assetsDirs' => Expect::arrayOf(Expect::string()),
		])->castTo('array');
	}

	public function loadConfiguration(): void
	{
		/** @var array<string, string> $config */
		$config = $this->getConfig();
		$builder = $this->getContainerBuilder();

		$builder->addDefinition($this->prefix('webpackAssetFactory'))
			->setType(WebpackAssetsFactory::class)
			->setArguments([
				'assetsDirs' => $config['assetsDirs'],
			]);
	}

	public function beforeCompile(): void
	{
		$builder = $this->getContainerBuilder();

		/** @var FactoryDefinition $latteFactory */
		$latteFactory = $builder->getDefinition('latte.latteFactory');
		$latteFactory->getResultDefinition()->addSetup(
			WebpackEncoreMacro::class . '::install(?->getCompiler())',
			['@self']
		);

		$latteFactory->getResultDefinition()->addSetup('addProvider', [
			'name' => 'webpackEncoreTagRenderer',
			'value' => $this->prefix('@webpackAssetFactory'),
		]);
	}
}
