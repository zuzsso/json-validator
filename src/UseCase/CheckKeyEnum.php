<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\JsonPayloadValidatorUnmanagedException;
use JsonValidator\Exception\ValueNotInListException;

interface CheckKeyEnum
{
    /**
     * @throws ValueNotInListException
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws JsonPayloadValidatorUnmanagedException
     */
    public function isEnum(string $key, array $payload, array $validValues, bool $required = true): self;
}
