<?php

declare(strict_types=1);

namespace JsonValidator\Exception;

class StringIsNotAnUrlException extends AbstractMalformedRequestBody
{
    public static function constructForStandardMessage(string $url): self
    {
        return new self("The string '$url' doesn't resemble an actual URL");
    }

    public function getErrorCode(): string
    {
        return 'requiredUrlFormat';
    }
}
