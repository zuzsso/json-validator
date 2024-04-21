<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\OptionalValueNotAStringException;
use JsonValidator\Exception\ValueStringEmptyException;

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
}
