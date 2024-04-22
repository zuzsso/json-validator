<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\InvalidIntegerValueException;
use JsonValidator\Exception\ValueNotEqualsToException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Service\KeyIntegerChecker;
use JsonValidator\Service\KeyPresenceChecker;
use JsonValidator\Service\ValueIntegerChecker;
use JsonValidator\Types\Range\IntValueRange;

class KeyIntegerCheckerTest extends CustomTestCase
{
    private KeyIntegerChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyIntegerChecker(new KeyPresenceChecker(), new ValueIntegerChecker());
    }

    public function shouldFailRequiredDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "Entry 'myKey' empty";

        $m3 = "Entry 'myKey' does not hold a valid int value";

        return [
            [$key, [], EntryMissingException::class, $m1],
            [$key, [$key => null], EntryEmptyException::class, $m2],
            [$key, [$key => []], EntryEmptyException::class, $m2],
            [$key, [$key => ''], EntryEmptyException::class, $m2],
            [$key, [$key => false], InvalidIntegerValueException::class, $m3],
            [$key, [$key => true], InvalidIntegerValueException::class, $m3],
            [$key, [$key => "0"], InvalidIntegerValueException::class, $m3],
            [$key, [$key => "blah"], InvalidIntegerValueException::class, $m3],
            [$key, [$key => 1.25], InvalidIntegerValueException::class, $m3],
            [$key, [$key => [[]]], InvalidIntegerValueException::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidIntegerValueException
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

    public function shouldPassRequiredDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [$key => 1]],
            [$key, [$key => 0]],
            [$key, [$key => -1]]
        ];
    }

    /**
     * @dataProvider shouldPassRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidIntegerValueException
     */
    public function testShouldPassRequired(
        string $key,
        array $payload
    ): void {
        $this->sut->required($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailOptionalDataProvider(): array
    {
        $key = 'myKey';
        $m3 = "Entry 'myKey' does not hold a valid int value";
        $m4 = "Entry 'myKey' empty";

        return [
            [$key, [$key => []], EntryEmptyException::class, $m4],
            [$key, [$key => ''], EntryEmptyException::class, $m4],
            [$key, [$key => false], InvalidIntegerValueException::class, $m3],
            [$key, [$key => true], InvalidIntegerValueException::class, $m3],
            [$key, [$key => "0"], InvalidIntegerValueException::class, $m3],
            [$key, [$key => "blah"], InvalidIntegerValueException::class, $m3],
            [$key, [$key => 1.25], InvalidIntegerValueException::class, $m3],
            [$key, [$key => [[]]], InvalidIntegerValueException::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidIntegerValueException
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

    public function shouldPassOptionalDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [$key => null]],
            [$key, []],
            [$key, [$key => 123]]
        ];
    }

    /**
     * @dataProvider shouldPassOptionalDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidIntegerValueException
     */
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailWithinRangeDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Range not correctly defined. min should be < than max, strictly";
        $m2 = "Entry 'myKey' missing";
        $m3 = "Entry 'myKey' empty";
        $m4 = "Entry 'myKey' is meant to be equals or greater than '3': '2'";
        $m5 = "Entry 'myKey' does not hold a valid int value";
        $m6 = "Entry 'myKey' is meant to be equals or less than '5': '6'";
        $m7 = "No range given";

        $fixedTests = [
            [$key, [], 1, null, true, EntryMissingException::class, $m2],
            [$key, [$key => null], 1, null, true, EntryEmptyException::class, $m3],
            [$key, [$key => ''], 1, null, true, EntryEmptyException::class, $m3],
            [$key, [$key => []], 1, null, true, EntryEmptyException::class, $m3],
        ];

        $variableTests = [];

        $variable = [true, false];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 123], null, null, $v, IncorrectParametrizationException::class, $m7];
            $variableTests[] = [$key, [$key => 123], 4, 4, $v, IncorrectParametrizationException::class, $m1];
            $variableTests[] = [$key, [$key => 123], 5, 4, $v, IncorrectParametrizationException::class, $m1];
            $variableTests[] = [$key, [$key => "blah"], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => "1"], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => true], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => false], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => [[]]], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => 1.1], 1, null, $v, InvalidIntegerValueException::class, $m5];
            $variableTests[] = [$key, [$key => 2], 3, null, $v, ValueTooSmallException::class, $m4];
            $variableTests[] = [$key, [$key => 2], 3, 5, $v, ValueTooSmallException::class, $m4];
            $variableTests[] = [$key, [$key => 6], null, 5, $v, ValueTooBigException::class, $m6];
            $variableTests[] = [$key, [$key => 6], 3, 5, $v, ValueTooBigException::class, $m6];
        }

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldFailWithinRangeDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws InvalidIntegerValueException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function testShouldFailWithinRange(
        string $key,
        array $payload,
        ?int $minValue,
        ?int $maxValue,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $range = new IntValueRange($minValue, $maxValue);

        $this->sut->withinRange($key, $payload, $range, $required);
    }

    public function shouldPassWithinRangeDataProvider(): array
    {
        $key = 'myKey';

        $fixedTests = [
            [$key, [], 1, null, false],
            [$key, [], 1, null, false],
            [$key, [], null, 2, false],
            [$key, [], 1, 2, false]
        ];

        $variable = [true, false];
        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 1], 1, null, $v];
            $variableTests[] = [$key, [$key => 1], 1, null, $v];
            $variableTests[] = [$key, [$key => 2], 1, null, $v];
            $variableTests[] = [$key, [$key => 2], null, 3, $v];
            $variableTests[] = [$key, [$key => 2], 2, 4, $v];
            $variableTests[] = [$key, [$key => 3], null, 3, $v];
            $variableTests[] = [$key, [$key => 3], 2, 4, $v];
            $variableTests[] = [$key, [$key => 4], 2, 4, $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassWithinRangeDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws InvalidIntegerValueException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     */
    public function testShouldPassWithinRange(
        string $key,
        array $payload,
        ?int $minValue,
        ?int $maxValue,
        bool $required
    ): void {
        $range = new IntValueRange($minValue, $maxValue);
        $this->sut->withinRange($key, $payload, $range, $required);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailEqualsToDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "Entry 'myKey' empty";
        $m3 = "Entry 'myKey' does not hold a valid int value";
        $m4 = "Entry 'myKey' is meant to be '6', but is '5'";

        $required = [true, false];

        $variableTests = [];

        foreach ($required as $r) {
            $variableTests[] = [$key, [$key => ''], 1, $r, EntryEmptyException::class, $m2];
            $variableTests[] = [$key, [$key => []], 1, $r, EntryEmptyException::class, $m2];
            $variableTests[] = [$key, [$key => "1"], 1, $r, InvalidIntegerValueException::class, $m3];
            $variableTests[] = [$key, [$key => 1.1], 1, $r, InvalidIntegerValueException::class, $m3];
            $variableTests[] = [$key, [$key => true], 1, $r, InvalidIntegerValueException::class, $m3];
            $variableTests[] = [$key, [$key => false], 1, $r, InvalidIntegerValueException::class, $m3];
            $variableTests[] = [$key, [$key => [[]]], 1, $r, InvalidIntegerValueException::class, $m3];
            $variableTests[] = [$key, [$key => 5], 6, $r, ValueNotEqualsToException::class, $m4];
        }

        $fixedTests = [
            [$key, [], 1, true, EntryMissingException::class, $m1],
            [$key, [$key => null], 1, true, EntryEmptyException::class, $m2],
        ];

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldFailEqualsToDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidIntegerValueException
     * @throws ValueNotEqualsToException
     */
    public function testShouldFailEqualsTo(
        string $key,
        array $payload,
        int $compareTo,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->sut->equalsTo($key, $payload, $compareTo, $required);
    }
}
