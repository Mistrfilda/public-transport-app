<?php

declare(strict_types=1);

namespace App\Utils;

use JsonSerializable;

class Coordinates implements JsonSerializable
{
	private float $latitude;

	private float $longitude;

	public function __construct(float $latitude, float $longitude)
	{
		$this->latitude = $latitude;
		$this->longitude = $longitude;
	}

	public function getLatitude(): float
	{
		return $this->latitude;
	}

	public function getLongitude(): float
	{
		return $this->longitude;
	}

	/**
	 * @return array<string, float>
	 */
	public function jsonSerialize(): array
	{
		return [
			'lat' => $this->getLatitude(),
			'lng' => $this->getLongitude(),
		];
	}
}
