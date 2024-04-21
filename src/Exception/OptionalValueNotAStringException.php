<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class OptionalValueNotAStringException extends AbstractMalformedRequestBody
{
    public static function constructForStandardMessage(): self
    {
        return new self("The value is optional, but if provided it has to be a non empty string");
    }

    public function getErrorCode(): string
    {
        return 'optionalValueNotString';
    }
}
