<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueNotAStringException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $key): self
    {
        return new self("The entry '$key' is not a string");
    }

    public function getErrorCode(): string
    {
        return 'expectedStringValue';
    }
}
