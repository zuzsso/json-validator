<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class RequiredArrayIsEmptyException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(): self
    {
        return new self("The array is required not to be empty");
    }

    public function getErrorCode(): string
    {
        return 'requiredNotEmptyArray';
    }
}
