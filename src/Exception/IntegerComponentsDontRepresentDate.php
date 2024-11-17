<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class IntegerComponentsDontRepresentDate extends AbstractUnrecognizedJsonStructure
{
    public static function constructForStandardMessage(int $year, int $month, int $day): self
    {
        return new self(
            "Cannot construct a date with year '$year', month '$month' and day '$day'"
        );
    }

    public function getErrorCode(): string
    {
        return 'integerComponentsNotADate';
    }
}
