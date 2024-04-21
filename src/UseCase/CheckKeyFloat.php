<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\OptionalPropertyNotAFloatException;
use JsonValidator\Exception\ValueNotAFloatException;
use JsonValidator\Exception\ValueNotEqualsToException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\FloatRange;

interface CheckKeyFloat
{
    /**
     * @throws ValueNotAFloatException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function required(string $key, array $payload): self;

    /**
     * @throws OptionalPropertyNotAFloatException
     */
    public function optional(string $key, array $payload): self;

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueNotAFloatException
     * @throws ValueTooSmallException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueTooBigException
     */
    public function withinRange(
        string $key,
        array $payload,
        FloatRange $range,
        bool $required = true
    ): self;

    /**
     * @throws ValueNotAFloatException
     * @throws ValueNotEqualsToException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function equalsTo(string $key, array $payload, float $compareTo, bool $required = true): self;
}
