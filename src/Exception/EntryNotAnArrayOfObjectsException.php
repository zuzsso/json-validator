<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class EntryNotAnArrayOfObjectsException extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(string $originalKey, string $subKey): self
    {
        return new self(
            "Entry '%originalKey%' should be an array of JSON objects, but item index '%subKey%' is not a JSON object",
            [
                'originalKey' => $originalKey,
                'subKey' => $subKey
            ]
        );
    }

    public static function constructForIndexNotNumericArrayIndex(string $key, string $index): self
    {
        return new self(
            "Entry '%key%' is an array but it contains not numeric indexes: '%index%'",
            [
                'key' => $key,
                'index' => $index
            ]
        );
    }

    public static function constructForCustomNumeration(string $key): self
    {
        return new self(
            "Entry '%key%' is an array but the first index is not 0 or the last index is not equals to the array count minus one",
            [
                'key' => $key
            ]
        );
    }

    public function getErrorCode(): string
    {
        return "notAnArrayOfJsonObjects";
    }
}
