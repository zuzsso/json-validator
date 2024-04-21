<?php

declare(strict_types=1);

namespace YourProject\HelloWorld;

use DiManifest\AbstractDependencyInjection;
use YourProject\HelloWorld\Service\HelloSayer;
use YourProject\HelloWorld\UseCase\SayHello;

use function DI\autowire;

class HelloWorldDiDefinitions extends AbstractDependencyInjection
{
    public static function getDependencies(): array
    {
        return [
            SayHello::class => autowire(HelloSayer::class)
        ];
    }
}
