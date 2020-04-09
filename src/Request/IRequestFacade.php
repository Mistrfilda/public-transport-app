<?php

declare(strict_types=1);

namespace App\Request;

interface IRequestFacade
{
	public function generateRequests(RequestConditions $conditions): void;
}
