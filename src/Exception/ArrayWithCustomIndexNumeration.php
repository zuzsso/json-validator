<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class ArrayWithCustomIndexNumeration extends AbstractMalformedRequestBody
{
    public static function constructForCustomNumeration(): self
    {
        return new self(
            "The array first index is not 0 or the last index is not equals to the array count minus one",
            []
        );
    }

    public function getErrorCode(): string
    {
        return 'arrayCustomNumeration';
    }
}
