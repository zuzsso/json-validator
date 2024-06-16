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
        $m3 = "The value 'jdoe@machine(comment). example' is meant to represent an email address, but it doesn't";
        $m4 = "The value 'first.(\")middle.last(\")@iana.org";

        $fixedTests = [
            ['', true, ValueStringEmptyException::class, $m1],
        ];

        $variables = [true, false];

        $varianTests = [];

        foreach ($variables as $v) {
            $varianTests[] = ['blah', $v, StringValueNotAnEmailException::class, $m2];
            $varianTests[] = ['blah', $v, StringValueNotAnEmailException::class, $m2];
            $varianTests[] = ['jdoe@machine(comment). example', $v, StringValueNotAnEmailException::class, $m3];
            $varianTests[] = ['first.(")middle.last(")@iana.org', $v, StringValueNotAnEmailException::class, $m4];
        }

        return array_merge($fixedTests, $varianTests);
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
        $fixedTests = [
            [null, false],
            ['', false]
        ];

        $variables = [true, false];

        $variantTests = [];

        foreach ($variables as $v) {
            $variantTests[] = ['a@b.com', $v];
            $variantTests[] = [' a@b.com', $v];
            $variantTests[] = ['first."mid\dle"."last"@iana.org', $v];
            $variantTests[] = ['bob@example.com', $v];
            $variantTests[] = [
                'first.last@x23456789012345678901234567890123456789012345678901234567890123.iana.org',
                $v
            ];
            $variantTests[] = ['my+1@gmail.com', $v];
            $variantTests[] = ['dclo@us.ibm.com', $v];
        }

        return array_merge($fixedTests, $variantTests);
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
