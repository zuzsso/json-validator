<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\UseCase\CheckKeyPresence;

class KeyPresenceChecker extends AbstractJsonChecker implements CheckKeyPresence
{
    /**
     * @inheritDoc
     */
    public function required(string $key, array $payload): self
    {
        if (!array_key_exists($key, $payload)) {
            throw EntryMissingException::constructForKeyNameMissing($key);
        }

        $e = EntryEmptyException::constructForKeyNameEmpty($key);

        $v = $payload[$key];

        if (empty($v) && ($v !== '0') && ($v !== 0) && ($v !== false)) {
            throw $e;
        }

        if (is_string($v) && trim($v) === '') {
            throw $e;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forbidden(string $key, array $payload): self
    {
        if (array_key_exists($key, $payload) && ($payload[$key] !== null)) {
            throw EntryForbiddenException::constructForKeyNameForbidden($key);
        }

        return $this;
    }
}
