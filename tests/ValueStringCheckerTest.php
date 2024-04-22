<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Service\ValueStringChecker;

class ValueStringCheckerTest extends CustomTestCase
{
    private ValueStringChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ValueStringChecker();
    }
}
