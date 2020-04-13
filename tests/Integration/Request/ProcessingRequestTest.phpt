<?php

declare(strict_types=1);

namespace Test\Integration\Request;

use Test\Integration\BaseTest;
use Tester\Assert;

$container = require __DIR__ . '/../TestsBootstrap.php';

class ProcessingRequestTest extends BaseTest
{
	public function testOne(): void
	{
		Assert::equal(1, 1);
	}
}
(new ProcessingRequestTest($container))->run();
