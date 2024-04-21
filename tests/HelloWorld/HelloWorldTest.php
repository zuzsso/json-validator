<?php

declare(strict_types=1);

namespace YourProject\Tests\HelloWorld;

use DI\DependencyException;
use DI\NotFoundException;
use YourProject\HelloWorld\Service\HelloSayer;
use YourProject\HelloWorld\UseCase\SayHello;
use YourProject\Tests\CustomTestCase;

class HelloWorldTest extends CustomTestCase
{
    private HelloSayer $sut;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setUp(): void
    {
        parent::setUp();

        // This is to illustrate how to use the DI container within tests. In reality, the System Under Test
        // should be explicitly instantiated (e.g. $this->sut = new SayHello())
        $this->sut = $this->diContainer->get(SayHello::class);
    }

    public function testCorrectlySaysHello(): void
    {
        $actual = $this->sut->sayIt();

        self::assertEquals('Hello world', $actual);
    }
}
