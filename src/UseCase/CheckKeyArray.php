<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidIntegerValueException;
use JsonValidator\Exception\OptionalPropertyNotAnArrayException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueArrayNotExactLengthException;
use JsonValidator\Exception\ValueNotAJsonObjectException;
use JsonValidator\Exception\ValueNotAnArrayException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\ArrayLengthRange;

interface CheckKeyArray
{
    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAnArrayException
     * @throws RequiredArrayIsEmptyException
     */
    public function requiredKey(string $key, array $payload): self;

    /**
     * @throws OptionalPropertyNotAnArrayException
     * @throws ValueNotAnArrayException
     */
    public function optionalKey(string $key, array $payload): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAJsonObjectException
     * @throws ValueNotAnArrayException
     */
    public function keyArrayOfJsonObjects(string $key, array $payload, bool $required = true): self;


    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAnArrayException
     * @throws IncorrectParametrizationException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function keyArrayOfLengthRange(
        string $key,
        array $payload,
        ArrayLengthRange $lengthRange,
        bool $required = true
    ): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueArrayNotExactLengthException
     * @throws ValueNotAnArrayException
     */
    public function keyArrayOfExactLength(
        string $key,
        array $payload,
        int $expectedLength,
        bool $required = true
    ): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAnArrayException
     * @throws ValueNotAStringException
     */
    public function keyArrayOfString(
        string $key,
        array $payload,
        bool $required = true
    ): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAnArrayException
     * @throws InvalidIntegerValueException
     */
    public function keyArrayOfInteger(
        string $key,
        array $payload,
        bool $required = true
    ): self;
}
