<?php

declare(strict_types=1);

namespace JsonValidator\Service;

use Throwable;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\JsonPayloadValidatorUnmanagedException;
use JsonValidator\Exception\ValueNotInListException;
use JsonValidator\UseCase\CheckKeyEnum;
use JsonValidator\UseCase\CheckKeyPresence;

class KeyEnumChecker extends AbstractJsonChecker implements CheckKeyEnum
{
    private CheckKeyPresence $checkPropertyPresence;

    public function __construct(CheckKeyPresence $checkPropertyPresence)
    {
        $this->checkPropertyPresence = $checkPropertyPresence;
    }

    /**
     * @inheritDoc
     */
    public function isEnum(string $key, array $payload, array $validValues, bool $required = true): self
    {
        if (!$required) {
            try {
                $this->checkPropertyPresence->forbidden($key, $payload);
                // The property does not exist in the payload, but is not required anyway, so return
                return $this;
            } catch (EntryForbiddenException $e) {
                // The property exists in the payload, so validate it
            }
        }

        $this->checkPropertyPresence->required($key, $payload);
        $value = $payload[$key];

        if (!in_array($value, $validValues, true)) {
            if (is_array($value)) {
                try {
                    $value = json_encode($value, JSON_THROW_ON_ERROR);
                } catch (Throwable $t) {
                    throw new JsonPayloadValidatorUnmanagedException($t->getMessage(), $t->getCode(), $t);
                }
            }
            throw ValueNotInListException::constructForList($key, $validValues, (string)$value);
        }

        return $this;
    }
}
