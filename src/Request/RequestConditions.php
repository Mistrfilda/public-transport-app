<?php

declare(strict_types=1);

namespace App\Request;

use JsonSerializable;

class RequestConditions implements JsonSerializable
{
	/** @var array<string, bool> */
	private $conditions;

	/** @var array<string, string> */
	private $parameters;

	/**
	 * @param array<string, bool> $conditions
	 * @param array<string, string> $parameters
	 */
	public function __construct(array $conditions = [], array $parameters = [])
	{
		$this->conditions = $conditions;
		$this->parameters = $parameters;
	}

	public function hasCondition(string $key): bool
	{
		return array_key_exists($key, $this->conditions);
	}

	public function getCondition(string $key): bool
	{
		if ($this->hasCondition($key)) {
			return $this->conditions[$key];
		}

		throw new RequestException('Unknown condition');
	}

	public function hasParameter(string $key): bool
	{
		return array_key_exists($key, $this->parameters);
	}

	public function getParameter(string $key): string
	{
		if ($this->hasParameter($key)) {
			return $this->parameters[$key];
		}

		throw new RequestException('Unknown parameter');
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize(): array
	{
		return [
			'conditions' => $this->conditions,
			'parameters' => $this->parameters,
		];
	}
}
