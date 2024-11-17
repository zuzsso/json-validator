<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class EntryForbiddenException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForKeyNameForbidden(string $key): self
    {
        return new self("Entry '$key' should not be present in the payload");
    }

    public function getErrorCode(): string
    {
        return 'propertyForbidden';
    }
}
