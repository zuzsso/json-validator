<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\OptionalPropertyNotAStringException;
use JsonValidator\Exception\StringIsNotAnUrlException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueStringNotExactLengthException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\StringByteLengthRange;

interface CheckKeyString
{
    /**
     * @throws ValueNotAStringException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function required(string $key, array $payload): self;

    /**
     * @throws OptionalPropertyNotAStringException
     */
    public function optional(string $key, array $payload): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAStringException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function byteLengthRange(
        string $key,
        array $payload,
        StringByteLengthRange $byteLengthRange,
        bool $required = true
    ): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws StringIsNotAnUrlException
     * @throws ValueNotAStringException
     */
    public function urlFormat(string $key, array $payload): self;

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueStringNotExactLengthException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAStringException
     */
    public function exactByteLength(string $key, array $payload, int $exactLength): self;

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidDateValueException
     * @throws ValueNotAStringException
     */
    public function dateTimeFormat(string $key, array $payload, string $dateFormat): self;
}
