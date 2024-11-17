<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use DI\DependencyException;
use DI\NotFoundException;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidJsonObjectValueExceptionStructure;
use JsonValidator\Service\KeyJsonObjectChecker;
use JsonValidator\UseCase\CheckKeyPresence;

class KeyJsonObjectCheckerTest extends CustomTestCase
{
    private KeyJsonObjectChecker $sut;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyJsonObjectChecker(
            $this->diContainer->get(CheckKeyPresence::class)
        );
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
            [$key, [$key => [1, 2, 3]], InvalidJsonObjectValueExceptionStructure::class, $m3],
            [$key, [$key => ["a", true, 1.3]], InvalidJsonObjectValueExceptionStructure::class, $m3],
            [$key, [$key => [[], []]], InvalidJsonObjectValueExceptionStructure::class, $m3],
        ];
    }

    /**
     * @dataProvider shouldFailRequiredDataProvider
     * @throws InvalidJsonObjectValueExceptionStructure
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
     * @throws InvalidJsonObjectValueExceptionStructure
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
            [$key, [$key => []], InvalidJsonObjectValueExceptionStructure::class, $m1],
            [$key, [$key => 1], InvalidJsonObjectValueExceptionStructure::class, $m1],
            [$key, [$key => ""], InvalidJsonObjectValueExceptionStructure::class, $m1],
        ];
    }

    /**
     * @dataProvider shouldFailOptionalDataProvider
     * @throws InvalidJsonObjectValueExceptionStructure
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
     * @throws InvalidJsonObjectValueExceptionStructure
     */
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }
}
