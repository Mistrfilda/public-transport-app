<?php

declare(strict_types=1);

namespace App\UI\Webpack;

use Nette\Utils\FileSystem;
use Nette\Utils\Html;
use Nette\Utils\Json;

class WebpackAssetsFactory
{
	private const ENTRYPOINT_NAME = 'entrypoints.json';

	/** @var string[] */
	private array $assetsDirs;

	/** @var string[] */
	private array $loadedAssets = [];

	/**
	 * @param string[] $assetsDirs
	 */
	public function __construct(array $assetsDirs)
	{
		$this->assetsDirs = $assetsDirs;
	}

	public function getCssAssets(string $entryName): string
	{
		$assets = $this->loadAssets();
		if (array_key_exists($entryName, $assets) === false) {
			throw new WebpackException(
				sprintf('Unknown entry name %s', $entryName)
			);
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
		if (array_key_exists($entryName, $assets) === false) {
			throw new WebpackException(
				sprintf('Unknown entry name %s', $entryName)
			);
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
		if (count($this->loadedAssets) > 0) {
			return $this->loadedAssets;
		}

		foreach ($this->assetsDirs as $assetDir) {
			$entryPointContents = FileSystem::read($assetDir . '/' . self::ENTRYPOINT_NAME);
			$decodedContents = Json::decode($entryPointContents, Json::FORCE_ARRAY)['entrypoints'];

			foreach ($decodedContents as $entryPointName => $contents) {
				if (array_key_exists($entryPointName, $this->loadedAssets)) {
					throw new WebpackException(
						sprintf('Duplicate entry name %s', $entryPointName)
					);
				}

				$this->loadedAssets[$entryPointName] = $contents;
			}
		}

		return $this->loadedAssets;
	}
}
