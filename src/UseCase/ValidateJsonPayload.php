<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

/**
 * Abstract interface, ideally all the payload validators should be implementing or extending this interface
 */
interface ValidateJsonPayload
{
    public function validate(array $jsonPayload): void;
}
