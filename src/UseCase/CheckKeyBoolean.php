<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidBoolValueException;

interface CheckKeyBoolean
{
    /**
     * @throws InvalidBoolValueException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function required(string $key, array $payload): self;

    /**
     * @throws InvalidBoolValueException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function optional(string $key, array $payload): self;
}
