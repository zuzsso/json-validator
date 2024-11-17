<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueNotEqualsToException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForInteger(string $key, int $compareTo, int $value): self
    {
        return new self("Entry '$key' is meant to be '$compareTo', but is '$value'");
    }

    public static function constructForFloat(string $key, float $compareTo, float $value): self
    {
        return new self("Entry '$key' is meant to be '$compareTo', but is '$value'");
    }

    public function getErrorCode(): string
    {
        return 'unexpectedValue';
    }
}
