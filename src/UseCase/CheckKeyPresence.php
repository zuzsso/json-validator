<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;

interface CheckKeyPresence
{
    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function required(string $key, array $payload): self;

    /**
     * @throws EntryForbiddenException
     */
    public function forbidden(string $key, array $payload): self;
}
