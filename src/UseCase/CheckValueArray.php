<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidIntegerValueException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueArrayNotExactLengthException;
use JsonValidator\Exception\ValueNotAJsonObjectException;
use JsonValidator\Exception\ValueNotAnArrayException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\ArrayLengthRange;

interface CheckValueArray
{
    /**
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAJsonObjectException
     * @throws ValueNotAnArrayException
     */
    public function arrayOfJsonObjects(array $arrayElements, bool $required = true): self;

    /**
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAStringException
     * @throws ValueNotAnArrayException
     */
    public function arrayOfString(array $arrayElements, bool $required = true): self;

    /**
     * @throws InvalidIntegerValueException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAnArrayException
     */
    public function arrayOfInteger(array $arrayElements, bool $required = true): self;

    /**
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAnArrayException
     */
    public function arrayOfLengthRange(
        array $payload,
        ArrayLengthRange $lengthRange
    ): self;

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueArrayNotExactLengthException
     * @throws ValueNotAnArrayException
     */
    public function arrayOfExactLength(array $payload, int $expectedLength): self;
}
