<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\StringValueNotAnEmailException;
use JsonValidator\Exception\ValueStringEmptyException;
use JsonValidator\Service\ValueStringChecker;

class ValueStringCheckerTest extends CustomTestCase
{
    private ValueStringChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ValueStringChecker();
    }

    public function shouldFailEmailValidationDataProvider(): array
    {
        $m1 = 'Expected a string, but got null or empty string';
        $m2 = "The value 'blah' is meant to represent an email address, but it doesn't";

        return [
            ['blah', false, StringValueNotAnEmailException::class, $m2],
            ['blah', true, StringValueNotAnEmailException::class, $m2],
            ['', true, ValueStringEmptyException::class, $m1],
        ];
    }

    /**
     * @dataProvider shouldFailEmailValidationDataProvider
     * @throws StringValueNotAnEmailException
     * @throws ValueStringEmptyException
     */
    public function testShouldFailEmailValidation(
        ?string $value,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->isEmailAddress($value, $required);
    }


    public function shouldAcceptEmailValidationDataProvider(): array
    {
        return [
            [null, false],
            ['', false],
            ['a@b.com', true],
            [' a@b.com', false],
        ];
    }

    /**
     * @dataProvider shouldAcceptEmailValidationDataProvider
     * @throws StringValueNotAnEmailException
     * @throws ValueStringEmptyException
     */
    public function testShouldAcceptEmailValidation(?string $value, bool $required): void
    {
        $this->expectNotToPerformAssertions();
        $this->sut->isEmailAddress($value, $required);
    }
}
