<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

use Exception;

class OptionalPropertyNotAnArrayException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForKey(string $key): self
    {
        return new self("Optional value is meant to be an array");
    }

    public function getErrorCode(): string
    {
        return 'optionalValueNotAnArray';
    }
}
