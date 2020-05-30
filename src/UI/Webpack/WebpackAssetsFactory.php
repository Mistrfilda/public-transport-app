<?php

declare(strict_types=1);

namespace App\UI\Webpack;

use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Nette\Utils\Json;

class WebpackAssetsFactory
{
	private const ENTRYPOINT_NAME = 'entrypoints.json';

	private string $buildedAssetsDir;

	/** @var string[] */
	private ?array $loadedAssets = null;

	public function __construct(string $buildedAssetsDir)
	{
		$this->buildedAssetsDir = $buildedAssetsDir;
	}

	public function getCssAssets(string $entryName): string
	{
		$assets = $this->loadAssets();
		if (! array_key_exists($entryName, $assets)) {
			throw new WebpackException('Unknown entry name "' . $entryName . '"');
		}

		$cssAssets = [];
		foreach ($assets[$entryName]['css'] as $cssAsset) {
			$link = Html::el('link')->addAttributes(
				[
					'rel' => 'stylesheet',
					'href' => $cssAsset,
				]
			);

			$cssAssets[] = $link->render();
		}

		return implode('', $cssAssets);
	}

	public function getJsAssets(string $entryName): string
	{
		$assets = $this->loadAssets();
		if (! array_key_exists($entryName, $assets)) {
			throw new WebpackException('Unknown entry name "' . $entryName . '"');
		}

		$jsAssets = [];
		foreach ($assets[$entryName]['js'] as $jsAsset) {
			$script = Html::el('script')->addAttributes(
				[
					'src' => $jsAsset,
					'type' => 'text/javascript',
				]
			);

			$jsAssets[] = $script->render();
		}

		return implode('', $jsAssets);
	}

	/**
	 * @return mixed[]
	 */
	private function loadAssets(): array
	{
		if ($this->loadedAssets !== null) {
			return $this->loadedAssets;
		}

		$entryPointContets = FileSystem::read($this->buildedAssetsDir . '/' . self::ENTRYPOINT_NAME);
		$this->loadedAssets = Json::decode($entryPointContets, Json::FORCE_ARRAY)['entrypoints'];
		return $this->loadedAssets;
	}
}
