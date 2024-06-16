<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\IntegerComponentsDontRepresentDate;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\IntValueRange;

interface CheckValueInteger
{
    /**
     * @throws IntegerComponentsDontRepresentDate
     */
    public function integerGroupRepresentsADate(int $year, int $month, int $day): void;

    /**
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function withinRange(
        int $value,
        IntValueRange $range
    ): self;
}
