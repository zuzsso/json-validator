<?php

declare(strict_types=1);

namespace JsonValidator\Types\Range;

use JsonValidator\Exception\IncorrectParametrizationException;

class StringByteLengthRange extends AbstractIntegerRange
{
    public function __construct(?int $min, ?int $max)
    {
        if ($min !== null && $min < 1) {
            throw new IncorrectParametrizationException(
                "Zero or negative range is not allowed as min value. Given: $min."
            );
        }

        if (($max !== null) && ($max < 1)) {

            throw new IncorrectParametrizationException("Values < 1 are not allowed as max count. Given: $max");
        }

        parent::__construct($min, $max);
    }
}
