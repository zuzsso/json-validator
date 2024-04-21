<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\InvalidIntegerValueException;
use JsonValidator\Exception\ValueNotEqualsToException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\IntValueRange;
use JsonValidator\UseCase\CheckKeyInteger;
use JsonValidator\UseCase\CheckKeyPresence;
use JsonValidator\UseCase\CheckValueInteger;

class KeyIntegerChecker extends AbstractJsonChecker implements CheckKeyInteger
{
    private CheckKeyPresence $checkPropertyPresence;
    private CheckValueInteger $checkValueInteger;

    public function __construct(CheckKeyPresence $checkPropertyPresence, CheckValueInteger $checkValueInteger)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
        $this->checkValueInteger = $checkValueInteger;
    }

    /**
     * @inheritDoc
     */
    public function required(string $key, array $payload): self
    {
        $this->checkPropertyPresence->required($key, $payload);

        $originalValue = $payload[$key];

        if (is_array($originalValue)) {
            throw InvalidIntegerValueException::constructForStandardMessage($key);
        }

        if (is_bool($originalValue)) {
            throw InvalidIntegerValueException::constructForStandardMessage($key);
        }

        if (is_string($originalValue)) {
            throw InvalidIntegerValueException::constructForStandardMessage($key);
        }

        $parsed = (int)$originalValue;

        if ((string)$parsed !== (string)$originalValue) {
            throw InvalidIntegerValueException::constructForStandardMessage($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function optional(string $key, array $payload): CheckKeyInteger
    {
        try {
            $this->checkPropertyPresence->forbidden($key, $payload);
            return $this;
        } catch (EntryForbiddenException $e) {
        }

        $this->required($key, $payload);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function withinRange(
        string $key,
        array $payload,
        IntValueRange $range,
        bool $required = true
    ): self {

        $minValue = $range->getMin();
        $maxValue = $range->getMax();

        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->required($key, $payload);

        $value = (int)$payload[$key];

        try {
            $this->checkValueInteger->withinRange($value, $range);
        } catch (ValueTooSmallException $v) {
            throw ValueTooSmallException::constructForKeyInteger($key, $minValue, $value);
        } catch (ValueTooBigException $v) {
            throw ValueTooBigException::constructForKeyInteger($key, $maxValue, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function equalsTo(string $key, array $payload, int $compareTo, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->checkPropertyPresence->required($key, $payload);
        $this->required($key, $payload);

        $value = (int)$payload[$key];

        if ($value !== $compareTo) {
            throw ValueNotEqualsToException::constructForInteger($key, $compareTo, $value);
        }

        return $this;
    }
}
