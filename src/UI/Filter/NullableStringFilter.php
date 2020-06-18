<?php

declare(strict_types=1);

namespace App\UI\Filter;

use App\UI\Front\Base\FrontDatagrid;

class NullableStringFilter
{
	public function format(?string $nullableString): string
	{
		if ($nullableString === null) {
			return FrontDatagrid::NULLABLE_PLACEHOLDER;
		}

		return $nullableString;
	}
}
