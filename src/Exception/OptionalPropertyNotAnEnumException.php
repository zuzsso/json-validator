<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class OptionalPropertyNotAnEnumException extends AbstractMalformedRequestBody
{
    public static function constructForList(string $key, array $listOfValidValues, string $givenValue): self
    {
        return new self(
            "The key '%key%' is optional, but if given has to be one of the following: [%values%]. Given: '%givenValue%'",
            [
                'key' => $key,
                'givenValue' => $givenValue,
                'values' => implode(' | ', $listOfValidValues)
            ]
        );
    }

    public function getErrorCode(): string
    {
        return 'unexpectedOptionalEnumValue';
    }
}
