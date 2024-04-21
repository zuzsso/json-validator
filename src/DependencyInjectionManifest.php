<?php

declare(strict_types=1);

namespace YourProject;

use DiManifest\AbstractDependencyInjection;
use YourProject\HelloWorld\HelloWorldDiDefinitions;

class DependencyInjectionManifest extends AbstractDependencyInjection
{
    public static function getDependencies(): array
    {
        return array_merge(
            HelloWorldDiDefinitions::getDependencies(),
            // Add any other dependency definitions according to php-di/php-di, that need to be published
        );
    }
}
