<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueArrayNotExactLengthException;
use JsonValidator\Exception\ValueNotAJsonObjectExceptionStructure;
use JsonValidator\Exception\ValueNotAnArrayException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Service\ValueArrayChecker;
use JsonValidator\Types\Range\ArrayLengthRange;
use PHPUnit\Framework\Attributes\DataProvider;

class ValueArrayCheckerTest extends CustomTestCase
{
    private ValueArrayChecker $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new ValueArrayChecker();
    }

    public static function shouldFailArrayOfJsonObjectsDataProvider(): array
    {
        $m1 = "The array is required not to be empty";
        $m2 = "Item index '0' is not a JSON object";
        $m3 = "Item index '1' is not a JSON object";

        $fixedTests = [
            [[], true, RequiredArrayIsEmptyException::class, $m1]
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [["blah"], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];

            $variableTests[] = [[1, 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];
            $variableTests[] = [['', 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];
            $variableTests[] = [['  ', 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];
            $variableTests[] = [[1.3, 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];
            $variableTests[] = [[null, 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m2];
            $variableTests[] = [[[], 2, 3], $v, ValueNotAJsonObjectExceptionStructure::class, $m3];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @throws ValueNotAnArrayException
     * @throws ValueNotAJsonObjectExceptionStructure
     * @throws RequiredArrayIsEmptyException
     */
    #[DataProvider('shouldFailArrayOfJsonObjectsDataProvider')]
    public function testShouldFailArrayOfJsonObjects(
        array $payload,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->arrayOfJsonObjects($payload, $required);
    }

    public static function shouldPassArrayOfJsonObjectsDataProvider(): array
    {
        return [
            [[], false],
            [[[]], false],
            [[[]], true],
        ];
    }

    /**
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAJsonObjectExceptionStructure
     * @throws ValueNotAnArrayException
     */
    #[DataProvider('shouldPassArrayOfJsonObjectsDataProvider')]
    public function testShouldPassArrayOfJsonObjects(array $payload, bool $required): void
    {
        $this->sut->arrayOfJsonObjects($payload, $required);
        $this->expectNotToPerformAssertions();
    }

    public static function shouldFailArrayOfLengthRangeDataProvider(): array
    {
        $m1 = "No range given";
        $m2 = "Zero or negative range is not allowed as min value. Given: 0.";
        $m3 = "Values < 1 are not allowed as max count.";
        $m4 = "Range not correctly defined. min should be < than max, strictly";
        $m5 = "Value is meant to be an array of minimum length of 2, but it is 1";
        $m6 = "Value is meant to be an array of maximum length of 1, but it is 2";
        $m7 = "Value is meant to be an array of maximum length of 2, but it is 3";
        $m8 = "Associative arrays not supported";

        return [
            [[], null, null, IncorrectParametrizationException::class, $m1],
            [[], 0, null, IncorrectParametrizationException::class, $m2],
            [[], null, 0, IncorrectParametrizationException::class, $m3],
            [[], 1, 1, IncorrectParametrizationException::class, $m4],
            [[], 2, 1, IncorrectParametrizationException::class, $m4],
            [[[]], 2, null, ValueTooSmallException::class, $m5],
            [[[]], 2, 3, ValueTooSmallException::class, $m5],
            [[[], []], null, 1, ValueTooBigException::class, $m6],
            [[[], [], []], 1, 2, ValueTooBigException::class, $m7],
            [["a" => []], 1, null, ValueNotAnArrayException::class, $m8],
        ];
    }

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     * @throws ValueNotAnArrayException
     */
    #[DataProvider('shouldFailArrayOfLengthRangeDataProvider')]
    public function testShouldFailArrayOfLengthRange(
        array $payload,
        ?int $minLength,
        ?int $maxLength,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $range = new ArrayLengthRange($minLength, $maxLength);
        $this->sut->arrayOfLengthRange($payload, $range);
    }

    public static function shouldPassArrayOfLengthRangeDataProvider(): array
    {
        return [
            [[[]], 1, null],
            [[[], []], 1, null],

            [[[]], null, 3],
            [[[], []], null, 3],
            [[[], [], []], null, 3],

            [[[]], 1, 3],
            [[[], []], 1, 3],
            [[[], [], []], 1, 3],
        ];
    }

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     * @throws ValueNotAnArrayException
     */
    #[DataProvider('shouldPassArrayOfLengthRangeDataProvider')]
    public function testShouldPassArrayOfLengthRange(array $payload, ?int $minLength, ?int $maxLength): void
    {
        $range = new ArrayLengthRange($minLength, $maxLength);
        $this->sut->arrayOfLengthRange($payload, $range);
        $this->expectNotToPerformAssertions();
    }

    public static function shouldFailArrayOfExactLengthDataProvider(): array
    {
        $m1 = "Min required length is 1";
        $m2 = "Value is expected to be an array of exact length of 1, but it is 0";
        $m3 = "Associative arrays not supported";

        return [
            [[], 0, IncorrectParametrizationException::class, $m1],
            [[], 1, ValueArrayNotExactLengthException::class, $m2],
            [["test" => "blah"], 1, ValueNotAnArrayException::class, $m3]
        ];
    }

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueArrayNotExactLengthException
     * @throws ValueNotAnArrayException
     */
    #[DataProvider('shouldFailArrayOfExactLengthDataProvider')]
    public function testShouldFailArrayOfExactLength(
        array $payload,
        int $expectedLength,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->sut->arrayOfExactLength($payload, $expectedLength);
    }

    public static function shouldPassArrayOfExactLengthDataProvider(): array
    {
        return [
            [["a", "b"], 2],
            [[[], [], []], 3],
            [[[], true, false, 1, 2, 1.3, 'Blah'], 7],
        ];
    }

    /**
     * @throws IncorrectParametrizationException
     * @throws ValueArrayNotExactLengthException
     * @throws ValueNotAnArrayException
     */
    #[DataProvider('shouldPassArrayOfExactLengthDataProvider')]
    public function testShouldPassArrayOfExactLength(array $payload, int $expectedLength): void
    {
        $this->sut->arrayOfExactLength($payload, $expectedLength);
        $this->expectNotToPerformAssertions();
    }
}
