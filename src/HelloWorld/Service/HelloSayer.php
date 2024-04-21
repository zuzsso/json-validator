<?php

declare(strict_types=1);

namespace YourProject\HelloWorld\Service;

use YourProject\HelloWorld\UseCase\SayHello;

class HelloSayer implements SayHello
{
    public function sayIt(): string
    {
        return 'Hello world';
    }
}
