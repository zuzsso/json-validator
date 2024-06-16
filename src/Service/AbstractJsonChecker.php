<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use JsonValidator\Exception\ValueNotAnArrayException;

abstract class AbstractJsonChecker
{
    /**
     * @throws ValueNotAnArrayException
     */
    protected function checkAllKeysAreNumericAndNoGaps(array $array): void
    {
        $count = count($array);

        if ($count === 0) {
            return;
        }

        $keys = array_keys($array);

        $min = null;
        $max = null;

        foreach ($keys as $k) {
            if (!is_int($k)) {
                throw ValueNotAnArrayException::associativeArraysNotSupported();
            }

            if ($min === null) {
                $min = $k;
            } elseif ($k < $min) {
                $min = $k;
            }

            if ($max === null) {
                $max = $k;
            } elseif ($k > $max) {
                $max = $k;
            }
        }

        if ($min !== 0) {
            throw ValueNotAnArrayException::firstArrayKeyNotZero();
        }

        $expectedMaxKey = $count - 1;

        if ($max !== $expectedMaxKey) {
            throw ValueNotAnArrayException::expectedLastKeyToBe($expectedMaxKey, $max);
        }
    }
}
