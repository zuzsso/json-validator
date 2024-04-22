<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueNotAnArrayException extends AbstractMalformedRequestBody
{
    public static function constructForStandardMessage(string $key): self
    {
        return new self(
            "Entry '$key' is expected to be an array",
        );
    }

    public static function associativeArraysNotSupported(): self
    {
        return new self("Associative arrays not supported");
    }

    public static function firstArrayKeyNotZero(): self
    {
        return new self("The first key of this array is not 0");
    }

    public static function expectedLastKeyToBe(int $expectedLastKey, int $actualLastKey): self
    {
        return new self("The last key is expected to be $expectedLastKey, but it is $actualLastKey");
    }

    public function getErrorCode(): string
    {
        return 'expectedArrayValue';
    }
}
