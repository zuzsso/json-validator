<?php

declare(strict_types=1);

namespace Tests\JsonValidator;

use PHPUnit\Framework\TestCase;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidJsonObjectValueException;
use JsonValidator\Service\KeyJsonObjectChecker;
use JsonValidator\Service\KeyPresenceChecker;

class KeyJsonObjectCheckerTest extends TestCase
{
    private KeyJsonObjectChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyJsonObjectChecker(new KeyPresenceChecker());
    }

    public function shouldFailRequiredDataProvider(): array
    {
        $key = 'myKey';

        $m1 = "Entry 'myKey' missing";
        $m2 = "Entry 'myKey' empty";
        $m3 = "The key 'myKey' is required and must point to a valid JSON object";
        return [
            [$key, [], EntryMissingException::class, $m1],
            [$key, [$key => null], EntryEmptyException::class, $m2],
            [$key, [$key => ''], EntryEmptyException::class, $m2],
            [$key, [$key => []], EntryEmptyException::class, $m2],
            [$key, [$key => [1, 2, 3]], InvalidJsonObjectValueException::class, $m3],
            [$key, [$key => ["a", true, 1.3]], InvalidJsonObjectValueException::class, $m3],
            [$key, [$key => [[], []]], InvalidJsonObjectValueException::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     * @throws InvalidJsonObjectValueException
     * @throws EntryEmptyException
     * @throws EntryMissingException
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
            [$key, [$key => ['a' => 1]]],
            [$key, [$key => ['a' => 1, 'b' => null, 'c' => true]]]
        ];
    }


    /**
     * @dataProvider shouldPassRequiredDataProvider
     *
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidJsonObjectValueException
     */
    public function testShouldPassRequired(string $key, array $payload): void
    {
        $this->sut->required($key, $payload);

        $this->expectNotToPerformAssertions();
    }

    public function shouldFailOptionalDataProvider(): array
    {
        $key = "myKey";
        $m1 = "The key 'myKey' is optional, but if provided, it must be a valid JSON object";

        return [
            [$key, [$key => []], InvalidJsonObjectValueException::class, $m1],
            [$key, [$key => 1], InvalidJsonObjectValueException::class, $m1],
            [$key, [$key => ""], InvalidJsonObjectValueException::class, $m1],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalDataProvider
     * @throws InvalidJsonObjectValueException
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
        $key = "myKey";

        return [
            [$key, []],
            [$key, [$key => null]],
            [$key, [$key => ['a' => 'blah']]],
        ];
    }

    /**
     * @dataProvider shouldPassOptionalDataProvider
     * @throws InvalidJsonObjectValueException
     */
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }
}
