<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class InvalidBoolValueException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $key): self
    {
        return new self(
            "The entry '$key' does not hold a valid boolean value"
        );
    }

    public function getErrorCode(): string
    {
        return 'notABooleanValue';
    }
}
