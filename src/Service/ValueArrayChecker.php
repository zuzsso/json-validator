<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidIntegerValueException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueArrayNotExactLengthException;
use JsonValidator\Exception\ValueNotAJsonObjectException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Types\Range\ArrayLengthRange;
use JsonValidator\UseCase\CheckValueArray;

class ValueArrayChecker extends AbstractJsonChecker implements CheckValueArray
{
    /**
     * @inheritDoc
     */
    public function arrayOfJsonObjects(array $arrayElements, bool $required = true): self
    {
        $count = count($arrayElements);

        if (($count === 0)) {
            if ($required === false) {
                return $this;
            }

            throw RequiredArrayIsEmptyException::constructForStandardMessage();
        }

        $this->checkAllKeysAreNumericAndNoGaps($arrayElements);

        foreach ($arrayElements as $i => $r) {
            if (!is_array($r)) {
                throw ValueNotAJsonObjectException::constructForStandardMessage((string)$i);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arrayOfString(array $arrayElements, bool $required = true): CheckValueArray
    {
        $count = count($arrayElements);

        if ($count === 0) {
            if ($required === false) {
                return $this;
            }

            throw RequiredArrayIsEmptyException::constructForStandardMessage();
        }

        $this->checkAllKeysAreNumericAndNoGaps($arrayElements);

        foreach ($arrayElements as $i => $r) {
            if (!is_string($r)) {
                throw ValueNotAStringException::constructForStandardMessage((string)$i);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arrayOfInteger(array $arrayElements, bool $required = true): CheckValueArray
    {
        $count = count($arrayElements);

        if ($count === 0) {
            if ($required === false) {
                return $this;
            }

            throw RequiredArrayIsEmptyException::constructForStandardMessage();
        }

        $this->checkAllKeysAreNumericAndNoGaps($arrayElements);

        foreach ($arrayElements as $i => $r) {
            if (!is_int($r)) {
                throw InvalidIntegerValueException::constructForStandardMessage((string)$i);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arrayOfLengthRange(
        array $payload,
        ArrayLengthRange $lengthRange
    ): CheckValueArray {
        $count = count($payload);

        $this->checkAllKeysAreNumericAndNoGaps($payload);

        $minLength = $lengthRange->getMin();
        $maxLength = $lengthRange->getMax();

        if (($minLength !== null) && ($count < $minLength)) {
            throw ValueTooSmallException::constructForValueArray($minLength, $count);
        }

        if (($maxLength !== null) && ($count > $maxLength)) {
            throw ValueTooBigException::constructForValueArrayLength($maxLength, $count);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function arrayOfExactLength(array $payload, int $expectedLength): CheckValueArray
    {
        if ($expectedLength <= 0) {
            throw new IncorrectParametrizationException('Min required length is 1');
        }

        $this->checkAllKeysAreNumericAndNoGaps($payload);

        $count = count($payload);

        if ($count !== $expectedLength) {
            throw ValueArrayNotExactLengthException::constructForValueArray($expectedLength, $count);
        }

        return $this;
    }
}
