<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueStringNotExactLengthException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $key, int $expectedLength, int $actualLength): self
    {
        return new self(
            "Entry '$key' is expected to be $expectedLength bytes long, but it is $actualLength"
        );
    }

    public function getErrorCode(): string
    {
        return 'expectedStringOfExactLength';
    }
}
