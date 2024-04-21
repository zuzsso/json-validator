<?php

declare(strict_types=1);

namespace JsonValidator;

use DiManifest\AbstractDependencyInjection;
use JsonValidator\Service\KeyArrayChecker;
use JsonValidator\Service\KeyBooleanChecker;
use JsonValidator\Service\KeyEnumChecker;
use JsonValidator\Service\KeyFloatChecker;
use JsonValidator\Service\KeyIntegerChecker;
use JsonValidator\Service\KeyJsonObjectChecker;
use JsonValidator\Service\KeyPresenceChecker;
use JsonValidator\Service\KeyStringChecker;
use JsonValidator\Service\ValueArrayChecker;
use JsonValidator\Service\ValueIntegerChecker;
use JsonValidator\Service\ValueStringChecker;
use JsonValidator\UseCase\CheckKeyArray;
use JsonValidator\UseCase\CheckKeyBoolean;
use JsonValidator\UseCase\CheckKeyEnum;
use JsonValidator\UseCase\CheckKeyFloat;
use JsonValidator\UseCase\CheckKeyInteger;
use JsonValidator\UseCase\CheckKeyJsonObject;
use JsonValidator\UseCase\CheckKeyPresence;
use JsonValidator\UseCase\CheckKeyString;
use JsonValidator\UseCase\CheckValueArray;
use JsonValidator\UseCase\CheckValueInteger;
use JsonValidator\UseCase\CheckValueString;
use Math\DependencyInjectionManifest as MDI;

use function DI\autowire;

class DependencyInjectionManifest extends AbstractDependencyInjection
{
    public static function getDependencies(): array
    {
        return array_merge(
            MDI::getDependencies(),
            [
                CheckKeyJsonObject::class => autowire(KeyJsonObjectChecker::class),
                CheckKeyArray::class => autowire(KeyArrayChecker::class),
                CheckKeyEnum::class => autowire(KeyEnumChecker::class),
                CheckKeyFloat::class => autowire(KeyFloatChecker::class),
                CheckKeyInteger::class => autowire(KeyIntegerChecker::class),
                CheckKeyPresence::class => autowire(KeyPresenceChecker::class),
                CheckKeyString::class => autowire(KeyStringChecker::class),
                CheckKeyBoolean::class => autowire(KeyBooleanChecker::class),
                CheckValueString::class => autowire(ValueStringChecker::class),
                CheckValueInteger::class => autowire(ValueIntegerChecker::class),
                CheckValueArray::class => autowire(ValueArrayChecker::class)
            ]
        );
    }
}
