<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class EntryMissingException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForKeyNameMissing(string $key): self
    {
        return new self("Entry '$key' missing");
    }

    public function getErrorCode(): string
    {
        return 'propertyRequired';
    }
}
