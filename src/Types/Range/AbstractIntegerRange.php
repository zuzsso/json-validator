<?php

declare(strict_types=1);

namespace JsonValidator\Types\Range;

use JsonValidator\Exception\IncorrectParametrizationException;

abstract class AbstractIntegerRange
{
    protected ?int $min;
    protected ?int $max;

    /**
     * @throws IncorrectParametrizationException
     */
    public function __construct(?int $min, ?int $max)
    {
        if (($min === null) && ($max === null)) {
            throw new IncorrectParametrizationException('No range given');
        }

        if (($min !== null) && ($max !== null) && ($min >= $max)) {
            throw new IncorrectParametrizationException(
                'Range not correctly defined. min should be < than max, strictly'
            );
        }

        $this->min = $min;
        $this->max = $max;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }
}
