<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ValueNotAJsonObjectException extends AbstractMalformedRequestBody
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
