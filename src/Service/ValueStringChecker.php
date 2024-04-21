<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use DateTimeImmutable;
use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\ValueStringEmptyException;
use JsonValidator\UseCase\CheckValueString;

class ValueStringChecker implements CheckValueString
{
    /**
     * @inheritDoc
     */
    public function required(?string $value): CheckValueString
    {
        $sanitized = trim($value . '');

        if ($sanitized === '') {
            throw ValueStringEmptyException::constructForStandardMessage();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dateTimeFormat(?string $value, string $dateFormat, bool $required = true): self
    {
        $sanitized = trim($value . '');

        if ($required) {
            $this->required($sanitized);
        } elseif ($sanitized === '') {
            return $this;
        }


        $parsed = DateTimeImmutable::createFromFormat($dateFormat, $sanitized);

        if ($parsed === false) {
            throw InvalidDateValueException::constructForValue($dateFormat, $sanitized);
        }

        $newDateFormatted = $parsed->format($dateFormat);

        if ($newDateFormatted !== $sanitized) {
            throw InvalidDateValueException::constructForValue($dateFormat, $sanitized);
        }

        return $this;
    }
}
