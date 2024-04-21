<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\InvalidBoolValueException;
use JsonValidator\UseCase\CheckKeyBoolean;
use JsonValidator\UseCase\CheckKeyPresence;

class KeyBooleanChecker extends AbstractJsonChecker implements CheckKeyBoolean
{
    private CheckKeyPresence $checkPropertyPresence;

    public function __construct(CheckKeyPresence $checkPropertyPresence)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
    }

    /**
     * @inheritDoc
     */
    public function required(string $key, array $payload): CheckKeyBoolean
    {
        $this->checkPropertyPresence->required($key, $payload);

        $originalValue = $payload[$key];

        if (!is_bool($originalValue)) {
            throw InvalidBoolValueException::constructForStandardMessage($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function optional(string $key, array $payload): CheckKeyBoolean
    {
        try {
            $this->checkPropertyPresence->forbidden($key, $payload);
            return $this;
        } catch (EntryForbiddenException $e) {
        }

        $this->required($key, $payload);

        return $this;
    }
}
