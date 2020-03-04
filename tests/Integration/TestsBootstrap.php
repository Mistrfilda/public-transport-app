<?php

declare(strict_types=1);

namespace Test\Integration;

use Tester\Environment;

require __DIR__ . '/../../vendor/autoload.php';

Environment::setup();

return Bootstrap::boot();