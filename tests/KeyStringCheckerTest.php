<?php

declare(strict_types=1);

namespace Tests\JsonValidator;

use PHPUnit\Framework\TestCase;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidDateValueException;
use JsonValidator\Exception\OptionalPropertyNotAStringException;
use JsonValidator\Exception\StringIsNotAnUrlException;
use JsonValidator\Exception\ValueNotAStringException;
use JsonValidator\Exception\ValueStringNotExactLengthException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Service\KeyPresenceChecker;
use JsonValidator\Service\KeyStringChecker;
use JsonValidator\Service\ValueStringChecker;
use JsonValidator\Types\Range\StringByteLengthRange;

class KeyStringCheckerTest extends TestCase
{
    private KeyStringChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyStringChecker(new KeyPresenceChecker(), new ValueStringChecker());
    }


    public function shouldPassRequiredDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [$key => 'abc']],
            [$key, [$key => '3']],
        ];
    }

    /**
     * @dataProvider shouldPassRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAStringException
     */
    public function testShouldPassRequired(string $key, array $payload): void
    {
        $this->sut->required($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailRequiredDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [], EntryMissingException::class, "Entry 'myKey' missing"],
            [$key, [$key => ''], EntryEmptyException::class, "Entry 'myKey' empty"],
            [$key, [$key => '    '], EntryEmptyException::class, "Entry 'myKey' empty"],
            [$key, [$key => []], EntryEmptyException::class, "Entry 'myKey' empty"],
            [$key, [$key => 3], ValueNotAStringException::class, "The entry 'myKey' is not a string"],
            [$key, [$key => 3.25], ValueNotAStringException::class, "The entry 'myKey' is not a string"],
            [$key, [$key => false], ValueNotAStringException::class, "The entry 'myKey' is not a string"],
            [$key, [$key => true], ValueNotAStringException::class, "The entry 'myKey' is not a string"],

        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAStringException
     */
    public function testShouldFailRequired(
        string $key,
        array $payload,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->required($key, $payload);
    }


    public function shouldPassOptionalDataProvider(): array
    {
        $key = 'myKey';
        return [
            [$key, []],
            [$key, [$key => 'abc']],
            [$key, [$key => null]]
        ];
    }

    /**
     * @dataProvider shouldPassOptionalDataProvider
     * @throws OptionalPropertyNotAStringException
     */
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailOptionalDataProvider(): array
    {
        $key = 'myKey';
        $msg001 = "The entry 'myKey' is optional, but if provided it should be a string";

        return [
            [$key, [$key => ''], OptionalPropertyNotAStringException::class, $msg001],
            [$key, [$key => '        '], OptionalPropertyNotAStringException::class, $msg001],
            [$key, [$key => 3], OptionalPropertyNotAStringException::class, $msg001],
            [$key, [$key => []], OptionalPropertyNotAStringException::class, $msg001],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalDataProvider
     * @throws OptionalPropertyNotAStringException
     */
    public function testShouldFailOptional(
        string $key,
        array $payload,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->optional($key, $payload);
    }


    public function byteLengthRangeShouldThrowFailValidationDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Zero or negative range is not allowed as min value. Given: -1.";
        $m2 = "Values < 1 are not allowed as max count. Given: -1";
        $m3 = "Range not correctly defined. min should be < than max, strictly";
        $m4 = "Entry 'myKey' is expected to be at least 2 bytes long, but it is 1";
        $m5 = "Entry 'myKey' is expected to be 3 bytes long maximum, but it is 4";
        $m6 = "Entry 'myKey' empty";
        $m7 = "The entry 'myKey' is not a string";
        $m8 = "Entry 'myKey' is expected to be 2 bytes long maximum, but it is 3";
        $m9 = "Zero or negative range is not allowed as min value. Given: 0.";
        $ma = "Values < 1 are not allowed as max count. Given: 0";
        $mb = "No range given";

        $fixedTests = [
            // These errors are about failure to configure the validator. They are not related to failed validation
            [$key, [$key => 'not relevant'], -1, null, true, IncorrectParametrizationException::class, $m1],
            [$key, [$key => 'not relevant'], null, -1, true, IncorrectParametrizationException::class, $m2],
            [$key, [$key => 'not relevant'], 4, 3, true, IncorrectParametrizationException::class, $m3],
            [$key, [$key => 'not relevant'], 4, 4, true, IncorrectParametrizationException::class, $m3],
        ];

        $variableTests = [];

        $variable = [true, false];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 'not relevant'], null, null, $v, IncorrectParametrizationException::class, $mb];
            $variableTests[] = [$key, [$key => 'not relevant'], 0, null, $v, IncorrectParametrizationException::class, $m9];
            $variableTests[] = [$key, [$key => 'not relevant'], null, 0, $v, IncorrectParametrizationException::class, $ma];
            $variableTests[] = [$key, [$key => ''], 2, 3, $v, EntryEmptyException::class, $m6];
            $variableTests[] = [$key, [$key => '    '], 2, 3, $v, EntryEmptyException::class, $m6];
            $variableTests[] = [$key, [$key => []], 2, 3, $v, EntryEmptyException::class, $m6];
            $variableTests[] = [$key, [$key => 6], 2, 3, $v, ValueNotAStringException::class, $m7];
            $variableTests[] = [$key, [$key => 6.66], 2, 3, $v, ValueNotAStringException::class, $m7];
            $variableTests[] = [$key, [$key => true], 2, 3, $v, ValueNotAStringException::class, $m7];
            $variableTests[] = [$key, [$key => false], 2, 3, $v, ValueNotAStringException::class, $m7];
            $variableTests[] = [$key, [$key => '1'], 2, 3, $v, ValueTooSmallException::class, $m4];
            $variableTests[] = [$key, [$key => '1'], 2, null, $v, ValueTooSmallException::class, $m4];

            // Seems like a three char string, but the validator will trim the trailing and leading whitespaces before
            // proceeding with the validation
            $variableTests[] = [$key, [$key => ' 1 '], 2, null, $v, ValueTooSmallException::class, $m4];

            $variableTests[] = [$key, [$key => '1111'], 2, 3, $v, ValueTooBigException::class, $m5];
            $variableTests[] = [$key, [$key => '1111'], null, 3, $v, ValueTooBigException::class, $m5];

            // Seems like a one char string, but we don't measure strings in chars, but in bytes, and this kanji is
            // three bytes long
            $variableTests[] = [$key, [$key => '大'], null, 2, $v, ValueTooBigException::class, $m8];
            $variableTests[] = [$key, [$key => '大'], 1, 2, $v, ValueTooBigException::class, $m8];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider byteLengthRangeShouldThrowFailValidationDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAStringException
     * @throws IncorrectParametrizationException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function testByteLengthRangeShouldFailValidation(
        string $key,
        array $payload,
        ?int $minLength,
        ?int $maximumLength,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $range = new StringByteLengthRange($minLength, $maximumLength);
        $this->sut->byteLengthRange($key, $payload, $range, $required);
    }

    public function shouldPassByteLengthRangeDataProvider(): array
    {
        $key = 'myKey';

        $fixedTests = [
            [$key, [$key => null], 1, 2, false]
        ];

        $variable = [false, true];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 'a'], 1, 2, $v];
            $variableTests[] = [$key, [$key => 'a'], 1, null, $v];
            $variableTests[] = [$key, [$key => 'a'], null, 1, $v];
            $variableTests[] = [$key, [$key => 'aa'], 2, 3, $v];
            $variableTests[] = [$key, [$key => 'aaa'], 2, 3, $v];
            $variableTests[] = [$key, [$key => '    aaa        '], 2, 3, $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassByteLengthRangeDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAStringException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function testShouldPassByteLengthRange(
        string $key,
        array $payload,
        ?int $minLength,
        ?int $maximumLength,
        bool $required
    ): void {
        $range = new StringByteLengthRange($minLength, $maximumLength);
        $this->sut->byteLengthRange($key, $payload, $range, $required);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailExactByteLengthDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Negative lengths not allowed, but you specified an exact length of '-1'";
        $m2 = "Zero lengths would require the 'optional' validator. Please correct the length";
        $m3 = "The entry 'myKey' is not a string";
        $m4 = "Entry 'myKey' empty";

        $fixedTests = [
            [$key, [$key => null], 1, true, EntryEmptyException::class, $m4]
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [], -1, $v, IncorrectParametrizationException::class, $m1];
            $variableTests[] = [$key, [], 0, $v, IncorrectParametrizationException::class, $m2];
            $variableTests[] = [$key, [$key => 1], 1, $v, ValueNotAStringException::class, $m3];
            $variableTests[] = [$key, [$key => true], 1, $v, ValueNotAStringException::class, $m3];
            $variableTests[] = [$key, [$key => false], 1, $v, ValueNotAStringException::class, $m3];
            $variableTests[] = [$key, [$key => 1.1], 1, $v, ValueNotAStringException::class, $m3];
            $variableTests[] = [$key, [$key => []], 1, $v, EntryEmptyException::class, $m4];
            $variableTests[] = [$key, [$key => ""], 1, $v, EntryEmptyException::class, $m4];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueStringNotExactLengthException
     * @throws ValueNotAStringException
     * @dataProvider shouldFailExactByteLengthDataProvider
     */
    public function testShouldFailExactByteLength(
        string $key,
        array $payload,
        int $exactLength,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->exactByteLength($key, $payload, $exactLength, $required);
    }

    public function shouldPassExactByteLengthDataProvider(): array
    {
        $key = 'myKey';

        $fixedTests = [
            [$key, [], 1, false],
            [$key, [$key => null], 1, false],
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 'a'], 1, $v];

            // Trailing and leading whitespaces are trimmed
            $variableTests[] = [$key, [$key => '     a     '], 1, $v];

            // Looks like a one char string, but we don't measure the length in chars, but in bytes
            $variableTests[] = [$key, [$key => '漢'], 3, $v];
        }

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldPassExactByteLengthDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAStringException
     * @throws ValueStringNotExactLengthException
     */
    public function testShouldPassExactByteLength(
        string $key,
        array $payload,
        int $exactLength,
        bool $required
    ): void {
        $this->sut->exactByteLength($key, $payload, $exactLength, $required);
        $this->expectNotToPerformAssertions();
    }


    public function shouldFailUrlFormatDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "The entry 'myKey' is not a string";
        $m3 = "Entry 'myKey' empty";
        $m4 = "The string 'abc' doesn't resemble an actual URL";

        $fixedTests = [
            [$key, [], true, EntryMissingException::class, $m1],
            [$key, ['myOtherKey' => 'blah'], true, EntryMissingException::class, $m1],
            [$key, [$key => 'abc'], true, StringIsNotAnUrlException::class, $m4]
        ];

        $variable = [true, false];
        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 123], $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => 1.3], $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => true], $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => false], $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => [[]]], $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => ''], $v, EntryEmptyException::class, $m3];
            $variableTests[] = [$key, [$key => []], $v, EntryEmptyException::class, $m3];
        }

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldFailUrlFormatDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws StringIsNotAnUrlException
     * @throws ValueNotAStringException
     */
    public function testShouldFailUrlFormat(
        string $key,
        array $payload,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->sut->urlFormat($key, $payload, $required);
    }

    public function shouldPassUrlFormatDataProvider(): array
    {
        $key = 'myKey';

        $fixedTests = [
            [$key, [], false],
            [$key, [$key => null], false],
        ];

        $variable = [false, true];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 'https://www.google.com'], $v];
            $variableTests[] = [$key, [$key => 'https://www.google.com/?a=1&b=2'], $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassUrlFormatDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws StringIsNotAnUrlException
     * @throws ValueNotAStringException
     */
    public function testShouldPassUrlFormat(
        string $key,
        array $payload,
        bool $required
    ): void {
        $this->sut->urlFormat($key, $payload, $required);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailDateTimeFormatDataProvider(): array
    {
        $dateFormat001 = 'Y-m-d';
        $date002 = '2024-03-25 23:00:59';

        $key = 'myKey';

        $m1 = "Entry 'myKey' empty";
        $m2 = "The entry 'myKey' is not a string";
        $m3 = "Entry 'myKey' does not hold a valid '$dateFormat001' date: '$date002'";

        $fixedTests = [
            [$key, [], $dateFormat001, true, EntryMissingException::class, "Entry 'myKey' missing"],
            [$key, [$key => null], $dateFormat001, true, EntryEmptyException::class, $m1],
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => ''], $dateFormat001, $v, EntryEmptyException::class, $m1];
            $variableTests[] = [$key, [$key => []], $dateFormat001, $v, EntryEmptyException::class, $m1];
            $variableTests[] = [$key, [$key => true], $dateFormat001, $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => false], $dateFormat001, $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => 1], $dateFormat001, $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => 1.1], $dateFormat001, $v, ValueNotAStringException::class, $m2];
            $variableTests[] = [$key, [$key => $date002], $dateFormat001, $v, InvalidDateValueException::class, $m3];
        }

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldFailDateTimeFormatDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidDateValueException
     * @throws ValueNotAStringException
     */
    public function testShouldFailDateTimeFormat(
        string $key,
        array $payload,
        string $dateTimeFormat,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->dateTimeFormat($key, $payload, $dateTimeFormat, $required);
    }

    public function shouldPassDateTimeFormatDataProvider(): array
    {
        $key = 'myKey';

        $dateFormat001 = 'Y-m-d H:i:s';
        $date002 = '2024-03-25 23:00:59';

        $fixedTests = [
            [$key, [], $dateFormat001, false],
            [$key, [$key => null], $dateFormat001, false],
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => $date002], $dateFormat001, $v];
            $variableTests[] = [$key, [$key => "    " . $date002 . "    "], $dateFormat001, $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassDateTimeFormatDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidDateValueException
     * @throws ValueNotAStringException
     */
    public function testShouldPassDateTimeFormat(
        string $key,
        array $payload,
        string $dateTimeFormat,
        bool $required
    ): void {
        $this->sut->dateTimeFormat($key, $payload, $dateTimeFormat, $required);
        $this->expectNotToPerformAssertions();
    }
}
