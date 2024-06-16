<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueTooSmallException extends AbstractMalformedRequestBody
{
    public static function constructForStringLength(string $key, int $expectedLength, int $actualLength): self
    {
        return new self(
            "Entry '$key' is expected to be at least $expectedLength bytes long, but it is $actualLength"
        );
    }

    public static function constructForKeyInteger(string $key, int $expectedValue, int $actualValue): self
    {
        return new self("Entry '$key' is meant to be equals or greater than '$expectedValue': '$actualValue'");
    }

    public static function constructForValueInteger(int $expectedValue, int $actualValue): self
    {
        return new self("The int value is meant to be equals or greater than '$expectedValue': '$actualValue'");
    }

    public static function constructForStandardFloat(string $key, float $expectedValue, float $actualValue): self
    {
        return new self("Entry '$key' is meant to be equals or greater than '$expectedValue': '$actualValue'");
    }

    public static function constructForKeyArray(string $key, int $expectedLength, int $actualLength): self
    {
        return new self(
            "Entry '$key' is meant to be an array of minimum length of $expectedLength, but it is $actualLength"
        );
    }

    public static function constructForValueArray(int $expectedLength, int $actualLength): self
    {
        return new self("Value is meant to be an array of minimum length of $expectedLength, but it is $actualLength");
    }

    public static function constructForValueStringByteLength(int $expectedByteLength, int $actualByteLength): self
    {
        return new self(
            "Value is meant to be a string of at least $expectedByteLength bytes, but it is $actualByteLength"
        );
    }

    public function getErrorCode(): string
    {
        return 'expectedMinValue';
    }
}
