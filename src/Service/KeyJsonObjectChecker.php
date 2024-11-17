<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidJsonObjectValueExceptionStructure;
use JsonValidator\UseCase\CheckKeyJsonObject;
use JsonValidator\UseCase\CheckKeyPresence;

class KeyJsonObjectChecker implements CheckKeyJsonObject
{
    private CheckKeyPresence $checkKeyPresence;

    public function __construct(CheckKeyPresence $checkKeyPresence)
    {
        $this->checkKeyPresence = $checkKeyPresence;
    }

    /**
     * @throws InvalidJsonObjectValueExceptionStructure
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function required(string $key, array $payload): CheckKeyJsonObject
    {
        $this->checkKeyPresence->required($key, $payload);

        $payloadValue = $payload[$key];

        if (!is_array($payloadValue)) {
            throw InvalidJsonObjectValueExceptionStructure::constructForRequiredKey($key);
        }

        foreach ($payloadValue as $subKey => $value) {
            if (!is_string($subKey)) {
                throw InvalidJsonObjectValueExceptionStructure::constructForRequiredKey($key);
            }
        }

        return $this;
    }

    /**
     * @throws InvalidJsonObjectValueExceptionStructure
     */
    public function optional(string $key, array $payload): CheckKeyJsonObject
    {
        try {
            $this->checkKeyPresence->forbidden($key, $payload);
            return $this;
        } catch (EntryForbiddenException $e) {
        }

        try {
            $this->required($key, $payload);
            return $this;
        } catch (EntryEmptyException | EntryMissingException | InvalidJsonObjectValueExceptionStructure $e) {
            throw InvalidJsonObjectValueExceptionStructure::constructForOptionalValue($key);
        }
    }
}
