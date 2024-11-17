<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class StringValueNotAnEmailException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $value): self
    {
        return new self(
            "The value '$value' is meant to represent an email address, but it doesn't"
        );
    }

    public function getErrorCode(): string
    {
        return 'stringNotEmail';
    }
}
