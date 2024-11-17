<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class InvalidDateValueException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $key, string $format, string $value): self
    {
        return new self("Entry '$key' does not hold a valid '$format' date: '$value'");
    }

    public static function constructForValue(string $format, string $value): self
    {
        return new self("String not in format '$format' date: '$value'");
    }

    public function getErrorCode(): string
    {
        return 'invalidDateFormat';
    }
}
