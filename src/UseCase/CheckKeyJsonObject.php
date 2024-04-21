<?php

declare(strict_types=1);

namespace JsonValidator\UseCase;

interface CheckKeyJsonObject
{
    public function required(string $key, array $payload): self;

    public function optional(string $key, array $payload): self;
}
