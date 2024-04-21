<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use DI\Container;
use PHPUnit\Framework\TestCase;

class CustomTestCase extends TestCase
{
    protected Container $diContainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->diContainer = $GLOBALS['diContainer'];
    }
}
