<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use DateTimeImmutable;
use JsonValidator\Exception\IntegerComponentsDontRepresentDate;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\IntValueRange;
use JsonValidator\UseCase\CheckValueInteger;

class ValueIntegerChecker implements CheckValueInteger
{
    /**
     * @inheritDoc
     */
    public function integerGroupRepresentsADate(int $year, int $month, int $day): void
    {
        $formattedYear = str_pad($year . '', 4, '0', STR_PAD_LEFT);
        $formattedMonth = str_pad($month . '', 2, '0', STR_PAD_LEFT);
        $formattedDay = str_pad($day . '', 2, '0', STR_PAD_LEFT);

        $tryDate = $formattedYear . '-' . $formattedMonth . '-' . $formattedDay;

        $success = DateTimeImmutable::createFromFormat('Y-m-d', $tryDate);

        $e = IntegerComponentsDontRepresentDate::constructForStandardMessage($year, $month, $day);

        if (!$success) {
            throw $e;
        }

        $converted = $success->format('Y-m-d');

        if ($converted !== $tryDate) {
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function withinRange(
        int $value,
        IntValueRange $range
    ): self {
        $minValue = $range->getMin();
        $maxValue = $range->getMax();

        if (($minValue !== null) && ($value < $minValue)) {
            throw ValueTooSmallException::constructForValueInteger($minValue, $value);
        }

        if (($maxValue !== null) && ($value > $maxValue)) {
            throw ValueTooBigException::constructForValueInteger($maxValue, $value);
        }

        return $this;
    }
}
