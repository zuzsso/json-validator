<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class InvalidJsonObjectValueException extends AbstractMalformedRequestBody
{
    public static function constructForRequiredKey(string $key): self
    {
        return new self("The key '$key' is required and must point to a valid JSON object");
    }

    public static function constructForOptionalValue(string $key): self
    {
        return new self("The key '$key' is optional, but if provided, it must be a valid JSON object");
    }

    public function getErrorCode(): string
    {
        return 'invalidJsonObject';
    }
}
