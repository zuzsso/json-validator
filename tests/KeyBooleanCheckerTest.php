<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidBoolValueException;
use JsonValidator\Service\KeyBooleanChecker;
use JsonValidator\Service\KeyPresenceChecker;

class KeyBooleanCheckerTest extends CustomTestCase
{
    private KeyBooleanChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyBooleanChecker(new KeyPresenceChecker());
    }

    public function shouldFailRequiredDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "Entry 'myKey' empty";
        $m3 = "The entry 'myKey' does not hold a valid boolean value";

        return [
            [$key, [], EntryMissingException::class, $m1],
            [$key, [$key => null], EntryEmptyException::class, $m2],
            [$key, [$key => ''], EntryEmptyException::class, $m2],
            [$key, [$key => '   '], EntryEmptyException::class, $m2],
            [$key, [$key => []], EntryEmptyException::class, $m2],
            [$key, [$key => 0], InvalidBoolValueException::class, $m3],
            [$key, [$key => "blah"], InvalidBoolValueException::class, $m3],
            [$key, [$key => 1], InvalidBoolValueException::class, $m3],
            [$key, [$key => "0"], InvalidBoolValueException::class, $m3],
            [$key, [$key => "1"], InvalidBoolValueException::class, $m3],
            [$key, [$key => [[]]], InvalidBoolValueException::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
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
            [$key, [$key => true]],
            [$key, [$key => false]]
        ];
    }

    /**
     * @dataProvider shouldPassRequiredDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    public function testShouldPassRequired(string $key, array $payload): void
    {
        $this->sut->required($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailOptionalDataProvider(): array
    {
        $key = 'myKey';
        $m2 = "Entry 'myKey' empty";
        $m3 = "The entry 'myKey' does not hold a valid boolean value";
        return [
            [$key, [$key => ''], EntryEmptyException::class, $m2],
            [$key, [$key => '   '], EntryEmptyException::class, $m2],
            [$key, [$key => []], EntryEmptyException::class, $m2],
            [$key, [$key => 0], InvalidBoolValueException::class, $m3],
            [$key, [$key => "blah"], InvalidBoolValueException::class, $m3],
            [$key, [$key => 1], InvalidBoolValueException::class, $m3],
            [$key, [$key => "0"], InvalidBoolValueException::class, $m3],
            [$key, [$key => "1"], InvalidBoolValueException::class, $m3],
            [$key, [$key => [[]]], InvalidBoolValueException::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
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
            [$key, []],
            [$key, [$key => null]],
            [$key, [$key => true]],
            [$key, [$key => false]]
        ];
    }

    /**
     * @dataProvider shouldPassOptionalDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }
}
