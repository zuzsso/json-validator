<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueTooBigException extends AbstractMalformedRequestBody
{
    public static function constructForStringLength(string $key, int $expectedLength, int $actualLength): self
    {
        return new self(
            "Entry '$key' is expected to be $expectedLength bytes long maximum, but it is $actualLength"
        );
    }

    public static function constructForKeyInteger(string $key, int $expected, int $actual): self
    {
        return new self("Entry '$key' is meant to be equals or less than '$expected': '$actual'");
    }

    public static function constructForValueInteger(int $expected, int $actual): self
    {
        return new self("Value integer is meant to be equals or less than '$expected': '$actual'");
    }

    public static function constructForFloat(string $key, float $expected, float $actual): self
    {
        return new self("Entry '$key' is meant to be equals or less than '$expected': '$actual'");
    }

    public static function constructForKeyArrayLength(string $key, int $expectedLength, int $actualLength): self
    {
        return new self("Entry '$key' is meant to be an array of maximum length of $expectedLength, but it is $actualLength");
    }

    public static function constructForValueArrayLength(int $expectedLength, int $actualLength): self
    {
        return new self("Value is meant to be an array of maximum length of $expectedLength, but it is $actualLength");
    }

    public static function constructForValueStringByteLength(int $expectedByteLength, int $actualByteLength): self
    {
        return new self (
            "Value is meant to be a string of $expectedByteLength bytes max, but it is $actualByteLength"
        );
    }

    public function getErrorCode(): string
    {
        return 'expectedMaxValue';
    }
}
