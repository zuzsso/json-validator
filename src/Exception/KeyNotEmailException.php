<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class KeyNotEmailException extends AbstractMalformedRequestBody
{
    public static function constructForStandardMessage(string $key, ?string $value)
    {
        $aux = $value . '';
        return new self("The key '$key' is meant to be an email address, but it isn't: '$aux'");
    }

    public function getErrorCode(): string
    {
        return 'keyNotEmail';
    }
}
