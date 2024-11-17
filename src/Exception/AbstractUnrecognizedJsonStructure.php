<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

use Exception;

abstract class AbstractUnrecognizedJsonStructure extends Exception
{
    abstract public function getErrorCode(): string;

    final public function serialize(): array
    {
        return [
            'code' => $this->getErrorCode(),
            'message' => $this->getMessage()
        ];
    }
}
