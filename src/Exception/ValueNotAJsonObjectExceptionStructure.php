<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueNotAJsonObjectExceptionStructure extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $subKey): self
    {
        return new self("Item index '$subKey' is not a JSON object");
    }

    public function getErrorCode(): string
    {
        return 'valueNotAJsonObject';
    }
}
