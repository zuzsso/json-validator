<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\KeyNotEmailException;
use Throwable;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\OptionalPropertyNotAStringException;
use JsonValidator\Exception\StringIsNotAnUrlException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueStringNotExactLengthException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\StringByteLengthRange;
use JsonValidator\UseCase\CheckKeyPresence;
use JsonValidator\UseCase\CheckKeyString;
use JsonValidator\UseCase\CheckValueString;

class KeyStringChecker extends AbstractJsonChecker implements CheckKeyString
{
    private CheckKeyPresence $checkPropertyPresence;
    private CheckValueString $checkValueString;

    public function __construct(CheckKeyPresence $checkPropertyPresence, CheckValueString $checkValueString)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
        $this->checkValueString = $checkValueString;
    }

    /**
     * @inheritDoc
     */
    public function required(string $key, array $payload): self
    {
        $this->checkPropertyPresence->required($key, $payload);

        $value = $payload[$key];

        if (!is_string($value)) {
            throw ValueNotAStringException::constructForStandardMessage($key);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function optional(string $key, array $payload): self
    {
        try {
            $this->checkPropertyPresence->forbidden($key, $payload);
            return $this;
        } catch (EntryForbiddenException $e) {
            // Property present, so make sure it is a string
            try {
                $this->required($key, $payload);
                return $this;
            } catch (EntryEmptyException | EntryMissingException | ValueNotAStringException $e) {
                throw OptionalPropertyNotAStringException::constructForStandardMessage($key);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function byteLengthRange(
        string $key,
        array $payload,
        StringByteLengthRange $byteLengthRange,
        bool $required = true
    ): CheckKeyString {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
                // The property exists, so validate it as if it was required
            }
        }

        $this->required($key, $payload);

        $maximumLength = $byteLengthRange->getMax();
        $minimumLength = $byteLengthRange->getMin();

        $trim = trim($payload[$key]);

        $length = strlen($trim);

        try {
            $this->checkValueString->byteLengthRange($trim, $byteLengthRange);
        } catch (ValueTooBigException $e) {
            throw ValueTooBigException::constructForStringLength($key, $maximumLength, $length);
        } catch (ValueTooSmallException $e) {
            throw ValueTooSmallException::constructForStringLength($key, $minimumLength, $length);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function urlFormat(string $key, array $payload, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
                // Continue validation as if it was required
            }
        }

        $this->required($key, $payload);

        $val = $payload[$key];
        if (!filter_var($val, FILTER_VALIDATE_URL)) {
            throw StringIsNotAnUrlException::constructForStandardMessage($val);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function exactByteLength(string $key, array $payload, int $exactLength, bool $required = true): self
    {
        if ($exactLength < 0) {
            throw new IncorrectParametrizationException(
                "Negative lengths not allowed, but you specified an exact length of '$exactLength'"
            );
        }

        if ($exactLength === 0) {
            throw new IncorrectParametrizationException(
                "Zero lengths would require the 'optional' validator. Please correct the length"
            );
        }

        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
                // Property exists, validate it as if it was required
            }
        }

        $this->required($key, $payload);

        $len = strlen(trim($payload[$key]));
        if ($len !== $exactLength) {
            throw ValueStringNotExactLengthException::constructForStandardMessage($key, $exactLength, $len);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function dateTimeFormat(string $key, array $payload, string $dateFormat, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->required($key, $payload);

        $value = trim((string)$payload[$key]);

        try {
            $this->checkValueString->dateTimeFormat($value, $dateFormat, $required);
        } catch (Throwable $t) {
            throw InvalidDateValueException::constructForStandardMessage($key, $dateFormat, $value);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function emailFormat(string $key, array $payload, bool $required): CheckKeyString
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                return $this;
            } catch (EntryForbiddenException $e) {
            }
        }

        $this->required($key, $payload);

        $value = trim((string)$payload[$key]);

        try {
            $this->checkValueString->isEmailAddress($value, $required);
        } catch (Throwable $t) {
            throw KeyNotEmailException::constructForStandardMessage($key, $value);
        }

        return $this;
    }
}
