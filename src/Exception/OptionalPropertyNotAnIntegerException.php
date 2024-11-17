<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class OptionalPropertyNotAnIntegerException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $key): self
    {
        return new self("The entry '$key' is optional, but if provided it should be an integer");
    }

    public function getErrorCode(): string
    {
        return 'entryOptionalNotInteger';
    }
}
