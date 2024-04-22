<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use Throwable;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\OptionalPropertyNotAFloatException;
use JsonValidator\Exception\ValueNotAFloatException;
use JsonValidator\Exception\ValueNotEqualsToException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\FloatRange;
use JsonValidator\UseCase\CheckKeyFloat;
use JsonValidator\UseCase\CheckKeyPresence;
use Math\Numbers\UseCase\EqualFloats;

class KeyFloatChecker extends AbstractJsonChecker implements CheckKeyFloat
{
    private CheckKeyPresence $checkPropertyPresence;
    private EqualFloats $equalFloats;

    public function __construct(CheckKeyPresence $checkPropertyPresence, EqualFloats $equalFloats)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
        $this->equalFloats = $equalFloats;
    }

    /**
     * @inheritDoc
     */
    public function required(string $key, array $payload): CheckKeyFloat
    {
        $this->checkPropertyPresence->required($key, $payload);

        $originalValue = $payload[$key];

        if (is_bool($originalValue)) {
            throw ValueNotAFloatException::constructForStandardMessage($key, (string)$originalValue);
        }

        if (is_string($originalValue)) {
            throw ValueNotAFloatException::constructForStringValue($key, $originalValue);
        }

        if (is_array($originalValue)) {
            throw ValueNotAFloatException::constructForGenericMessage($key);
        }

        $castValue = (float)$originalValue;

        if (((string)$castValue) !== ((string)$originalValue)) {
            throw ValueNotAFloatException::constructForStandardMessage($key, (string)$originalValue);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function optional(string $key, array $payload): CheckKeyFloat
    {
        try {
            $this->checkPropertyPresence->forbidden($key, $payload);
        } catch (EntryForbiddenException $e) {
            // Property exists, so make sure it is a float
            try {
                $this->required($key, $payload);
            } catch (Throwable $t) {
                throw OptionalPropertyNotAFloatException::constructForStandardMessage($key);
            }
        }

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function withinRange(
        string $key,
        array $payload,
        FloatRange $range,
        bool $required = true
    ): self {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->required($key, $payload);

        $value = (float)$payload[$key];

        $minValue = $range->getMin();
        $maxValue = $range->getMax();

        if ($minValue !== null) {
            $equals = $this->equalFloats->equalFloats($minValue, $value);
            if ((!$equals) && ($value < $minValue)) {
                throw ValueTooSmallException::constructForStandardFloat($key, $minValue, $value);
            }
        }

        if ($maxValue !== null) {
            $equals = $this->equalFloats->equalFloats($maxValue, $value);
            if ((!$equals) && ($value > $maxValue)) {
                throw ValueTooBigException::constructForFloat($key, $maxValue, $value);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function equalsTo(string $key, array $payload, float $compareTo, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->required($key, $payload);

        $value = (float)$payload[$key];

        $equals = $this->equalFloats->equalFloats($value, $compareTo);

        if (!$equals) {
            throw ValueNotEqualsToException::constructForFloat($key, $compareTo, $value);
        }

        return $this;
    }
}
