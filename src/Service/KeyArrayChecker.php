<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\OptionalPropertyNotAnArrayException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueArrayNotExactLengthException;
use JsonValidator\Exception\ValueNotAnArrayException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\ArrayLengthRange;
use JsonValidator\UseCase\CheckKeyArray;
use JsonValidator\UseCase\CheckKeyPresence;
use JsonValidator\UseCase\CheckValueArray;

class KeyArrayChecker extends AbstractJsonChecker implements CheckKeyArray
{
    private CheckKeyPresence $checkPropertyPresence;
    private CheckValueArray $checkValueArray;

    public function __construct(CheckKeyPresence $checkPropertyPresence, CheckValueArray $checkValueArray)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
        $this->checkValueArray = $checkValueArray;
    }

    /**
     * @inheritDoc
     */
    public function requiredKey(string $key, array $payload): self
    {
        $this->checkPropertyPresence->required($key, $payload);

        $value = $payload[$key];

        if (!is_array($value)) {
            throw ValueNotAnArrayException::constructForStandardMessage($key);
        }

        $count = count($value);

        if ($count === 0) {
            throw RequiredArrayIsEmptyException::constructForStandardMessage();
        }

        $this->checkAllKeysAreNumericAndNoGaps($value);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function optionalKey(string $key, array $payload): CheckKeyArray
    {
        try {
            $this->checkPropertyPresence->forbidden($key, $payload);
            return $this;
        } catch (EntryForbiddenException $e) {
        }

        $value = $payload[$key];

        if (!is_array($value)) {
            throw OptionalPropertyNotAnArrayException::constructForKey($key);
        }


        if (count($value) === 0) {
            return $this;
        }

        try {
            $this->requiredKey($key, $payload);
        } catch (EntryEmptyException | EntryMissingException $e) {
            return $this;
        } catch (RequiredArrayIsEmptyException $e) {
            throw OptionalPropertyNotAnArrayException::constructForKey($key);
        }


        return $this;
    }

    /**
     * @inheritDoc
     */
    public function keyArrayOfJsonObjects(string $key, array $payload, bool $required = true): self
    {
        if ($required === false) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }


        $this->requiredKey($key, $payload);

        $arrayElements = $payload[$key];

        $this->checkValueArray->arrayOfJsonObjects($arrayElements);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function keyArrayOfExactLength(string $key, array $payload, int $expectedLength, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->requiredKey($key, $payload);

        $value = $payload[$key];

        try {
            $this->checkValueArray->arrayOfExactLength($value, $expectedLength);
        } catch (ValueArrayNotExactLengthException $e) {
            throw ValueArrayNotExactLengthException::constructForKeyArray($key, $expectedLength, count($value));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function keyArrayOfLengthRange(
        string $key,
        array $payload,
        ArrayLengthRange $lengthRange,
        bool $required = true
    ): self {
        if ($required === false) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->requiredKey($key, $payload);

        try {
            $this->checkValueArray->arrayOfLengthRange($payload[$key], $lengthRange);
        } catch (ValueTooBigException $e) {
            throw ValueTooBigException::constructForKeyArrayLength($key, $lengthRange->getMax(), count($payload[$key]));
        } catch (ValueTooSmallException $e) {
            throw ValueTooSmallException::constructForKeyArray($key, $lengthRange->getMin(), count($payload[$key]));
        }

        return $this;
    }
}
