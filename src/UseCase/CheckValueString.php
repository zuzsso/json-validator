<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\OptionalValueNotAStringException;
use JsonValidator\Exception\StringValueNotAnEmailException;
use JsonValidator\Exception\ValueStringEmptyException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\StringByteLengthRange;

interface CheckValueString
{
    /**
     * @throws ValueStringEmptyException
     */
    public function required(?string $value): self;

    /**
     * @throws InvalidDateValueException
     * @throws OptionalValueNotAStringException
     * @throws ValueStringEmptyException
     */
    public function dateTimeFormat(?string $value, string $dateFormat, bool $required = true): self;

    /**
     * @throws StringValueNotAnEmailException
     * @throws ValueStringEmptyException
     */
    public function isEmailAddress(?string $value, bool $required = true): self;

    /**
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function byteLengthRange(string $value, StringByteLengthRange $byteLengthRange): self;
}
