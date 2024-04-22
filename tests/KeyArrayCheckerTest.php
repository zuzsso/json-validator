<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\IncorrectParametrizationException;
use JsonValidator\Exception\OptionalPropertyNotAnArrayException;
use JsonValidator\Exception\RequiredArrayIsEmptyException;
use JsonValidator\Exception\ValueNotAJsonObjectException;
use JsonValidator\Exception\ValueNotAnArrayException;
use JsonValidator\Exception\ValueTooBigException;
use JsonValidator\Exception\ValueTooSmallException;
use JsonValidator\Service\KeyArrayChecker;
use JsonValidator\Service\KeyPresenceChecker;
use JsonValidator\Service\ValueArrayChecker;
use JsonValidator\Types\Range\ArrayLengthRange;

class KeyArrayCheckerTest extends CustomTestCase
{
    private KeyArrayChecker $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new KeyArrayChecker(new KeyPresenceChecker(), new ValueArrayChecker());
    }

    public function shouldFailRequiredDataProvider(): array
    {
        $key = 'myKey';
        $m1 = "Entry '$key' missing";
        $m2 = "Entry '$key' empty";
        $m3 = "Entry '$key' is expected to be an array";
        $m4 = "Associative arrays not supported";
        $m5 = "The first key of this array is not 0";
        $m6 = "The last key is expected to be 1, but it is 2";

        return [
            [$key, [], EntryMissingException::class, $m1],
            [$key, [$key => ''], EntryEmptyException::class, $m2],
            [$key, [$key => '   '], EntryEmptyException::class, $m2],
            [$key, [$key => []], EntryEmptyException::class, $m2],
            [$key, [$key => null], EntryEmptyException::class, $m2],

            [$key, [$key => 0], ValueNotAnArrayException::class, $m3],
            [$key, [$key => "blah"], ValueNotAnArrayException::class, $m3],
            [$key, [$key => true], ValueNotAnArrayException::class, $m3],
            [$key, [$key => false], ValueNotAnArrayException::class, $m3],
            [$key, [$key => 3.25], ValueNotAnArrayException::class, $m3],

            [$key, [$key => [0 => "a", "test" => "b"]], ValueNotAnArrayException::class, $m4],

            [$key, [$key => [1 => "a", 2 => "b"]], ValueNotAnArrayException::class, $m5],
            [$key, [$key => [0 => "a", 2 => "b"]], ValueNotAnArrayException::class, $m6],
        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAnArrayException
     */
    public function testShouldFailRequiredKey(
        string $key,
        array $payload,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->requiredKey($key, $payload);
    }

    public function shouldPassRequiredDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [$key => [[]]]],
            [$key, [$key => ["a", "b"]]],
            [$key, [$key => ["a", 1, true, 1.3, false]]],
        ];
    }

    /**
     * @dataProvider shouldPassRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotAnArrayException
     * @throws RequiredArrayIsEmptyException
     */
    public function testShouldPassRequiredKey(string $key, array $payload): void
    {
        $this->sut->requiredKey($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailOptionalKeyDataProvider(): array
    {
        $key = 'myKey';
        $m4 = "Associative arrays not supported";
        $m5 = "The first key of this array is not 0";
        $m6 = "The last key is expected to be 1, but it is 2";
        $m7 = "Optional value is meant to be an array";

        return [
            [$key, [$key => ''], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => '   '], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => 0], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => "blah"], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => true], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => false], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => 3.25], OptionalPropertyNotAnArrayException::class, $m7],
            [$key, [$key => [0 => "a", "test" => "b"]], ValueNotAnArrayException::class, $m4],
            [$key, [$key => [1 => "a", 2 => "b"]], ValueNotAnArrayException::class, $m5],
            [$key, [$key => [0 => "a", 2 => "b"]], ValueNotAnArrayException::class, $m6],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalKeyDataProvider
     * @throws ValueNotAnArrayException
     * @throws OptionalPropertyNotAnArrayException
     */
    public function testShouldFailOptionalKey(
        string $key,
        array $payload,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->sut->optionalKey($key, $payload);
    }

    public function shouldPassOptionalKeyDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, []],
            [$key, [$key => null]],
            [$key, [$key => []]],
            [$key, [$key => [[]]]],

        ];
    }

    /**
     * @dataProvider shouldPassOptionalKeyDataProvider
     * @dataProvider shouldPassRequiredDataProvider
     *
     * @throws OptionalPropertyNotAnArrayException
     * @throws ValueNotAnArrayException
     */
    public function testShouldPassOptionalKey(string $key, array $payload): void
    {
        $this->sut->optionalKey($key, $payload);
        $this->expectNotToPerformAssertions();
    }


    public function shouldFailKeyOfJsonObjectsDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "Entry 'myKey' empty";
        $m3 = "Entry 'myKey' is expected to be an array";
        $m4 = "Item index '0' is not a JSON object";
        $m5 = "Item index '1' is not a JSON object";

        $fixedTests = [
            [$key, [], true, EntryMissingException::class, $m1],
            [$key, [$key => null], true, EntryEmptyException::class, $m2]
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => []], $v, EntryEmptyException::class, $m2];
            $variableTests[] = [$key, [$key => 'a'], $v, ValueNotAnArrayException::class, $m3];
            $variableTests[] = [$key, [$key => '  '], $v, EntryEmptyException::class, $m2];
            $variableTests[] = [$key, [$key => 1], $v, ValueNotAnArrayException::class, $m3];
            $variableTests[] = [$key, [$key => [1, 2, 3]], $v, ValueNotAJsonObjectException::class, $m4];
            $variableTests[] = [$key, [$key => [[], 2, 3]], $v, ValueNotAJsonObjectException::class, $m5];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldFailKeyOfJsonObjectsDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAJsonObjectException
     * @throws ValueNotAnArrayException
     */
    public function testShouldFailKeyOfJsonObjects(
        string $key,
        array $payload,
        bool $required,
        string $expectedException,
        string $expectedExceptionmessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionmessage);
        $this->sut->keyArrayOfJsonObjects($key, $payload, $required);
    }


    public function shouldPassKeyOfJsonObjectsDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [], false],
            [$key, [$key => null], false],
            [$key, [$key => [[]]], true]
        ];
    }

    /**
     * @dataProvider shouldPassKeyOfJsonObjectsDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws RequiredArrayIsEmptyException
     * @throws ValueNotAJsonObjectException
     * @throws ValueNotAnArrayException
     */
    public function testShouldPassKeyOfJsonObjects(
        string $key,
        array $payload,
        bool $required = true
    ): void {
        $this->sut->keyArrayOfJsonObjects($key, $payload, $required);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailKeyArrayOfLengthRangeDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "No range given";
        $m2 = "Range not correctly defined. min should be < than max, strictly";
        $m3 = "Zero or negative range is not allowed as min value. Given: -1.";
        $m4 = "Values < 1 are not allowed as max count.";
        $m5 = "Entry 'myKey' missing";
        $m6 = "Entry 'myKey' empty";
        $m7 = "Entry 'myKey' is expected to be an array";
        $m8 = "Entry 'myKey' is meant to be an array of minimum length of 3, but it is 2";
        $m9 = "Entry 'myKey' is meant to be an array of maximum length of 2, but it is 3";
        $ma = "Zero or negative range is not allowed as min value. Given: 0.";

        $fixtedTests = [
            [$key, [], 1, null, true, EntryMissingException::class, $m5],
            [$key, [$key => null], 1, null, true, EntryEmptyException::class, $m6]
        ];

        $variableTests = [];

        $variables = [true, false];

        foreach ($variables as $v) {
            $variableTests[] = [$key, [], null, null, $v, IncorrectParametrizationException::class, $m1];
            $variableTests[] = [$key, [], 2, 1, $v, IncorrectParametrizationException::class, $m2];
            $variableTests[] = [$key, [], 2, 2, $v, IncorrectParametrizationException::class, $m2];
            $variableTests[] = [$key, [], -1, 2, $v, IncorrectParametrizationException::class, $m3];
            $variableTests[] = [$key, [], 0, 2, $v, IncorrectParametrizationException::class, $ma];
            $variableTests[] = [$key, [], null, 0, $v, IncorrectParametrizationException::class, $m4];
            $variableTests[] = [$key, [$key => ''], 1, 3, $v, EntryEmptyException::class, $m6];
            $variableTests[] = [$key, [$key => 'blah'], 1, 3, $v, ValueNotAnArrayException::class, $m7];
            $variableTests[] = [$key, [$key => 1], 1, 3, $v, ValueNotAnArrayException::class, $m7];
            $variableTests[] = [$key, [$key => 1.1], 1, 3, $v, ValueNotAnArrayException::class, $m7];
            $variableTests[] = [$key, [$key => true], 1, 3, $v, ValueNotAnArrayException::class, $m7];
            $variableTests[] = [$key, [$key => false], 1, 3, $v, ValueNotAnArrayException::class, $m7];
            $variableTests[] = [$key, [$key => [1, 'blah']], 3, null, $v, ValueTooSmallException::class, $m8];
            $variableTests[] = [$key, [$key => [1, 'blah', 1.23]], null, 2, $v, ValueTooBigException::class, $m9];
        }

        return array_merge($fixtedTests, $variableTests);
    }

    /**
     * @dataProvider shouldFailKeyArrayOfLengthRangeDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAnArrayException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     * @throws RequiredArrayIsEmptyException
     */
    public function testShouldFailKeyArrayOfLengthRange(
        string $key,
        array $payload,
        ?int $minCount,
        ?int $maxCount,
        bool $required,
        string $expectedException,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $range = new ArrayLengthRange($minCount, $maxCount);
        $this->sut->keyArrayOfLengthRange($key, $payload, $range, $required);
    }

    public function shouldPassKeyArrayOfLengthRangeDataProvider(): array
    {
        $key = 'myKey';

        $fixedTests = [
            [$key, [], null, 2, false],
            [$key, [$key => null], null, 2, false]
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => [[]]], null, 2, $v];
            $variableTests[] = [$key, [$key => [[], []]], null, 2, $v];
            $variableTests[] = [$key, [$key => [[]]], 1, null, $v];
            $variableTests[] = [$key, [$key => [[], []]], 1, null, $v];
            $variableTests[] = [$key, [$key => [[]]], 1, 3, $v];
            $variableTests[] = [$key, [$key => [[], []]], 1, 3, $v];
            $variableTests[] = [$key, [$key => [[], [], []]], 1, 3, $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassKeyArrayOfLengthRangeDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws IncorrectParametrizationException
     * @throws ValueNotAnArrayException
     * @throws ValueTooBigException
     * @throws ValueTooSmallException
     * @throws RequiredArrayIsEmptyException
     */
    public function testShouldPassKeyArrayOfLengthRange(
        string $key,
        array $payload,
        ?int $minCount,
        ?int $maxCount,
        $required
    ): void {
        $lenghRange = new ArrayLengthRange($minCount, $maxCount);
        $this->sut->keyArrayOfLengthRange($key, $payload, $lenghRange, $required);
        $this->expectNotToPerformAssertions();
    }
}
