<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use DI\DependencyException;
use DI\NotFoundException;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\InvalidBoolValueException;
use JsonValidator\Service\KeyBooleanChecker;
use JsonValidator\UseCase\CheckKeyPresence;
use PHPUnit\Framework\Attributes\DataProvider;

class KeyBooleanCheckerTest extends CustomTestCase
{
    private KeyBooleanChecker $sut;

    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyBooleanChecker(
            $this->diContainer->get(CheckKeyPresence::class)
        );
    }

    public static function shouldFailRequiredDataProvider(): array
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
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    #[DataProvider('shouldFailRequiredDataProvider')]
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

    public static function shouldPassRequiredDataProvider(): array
    {
        $key = 'myKey';

        return [
            [$key, [$key => true]],
            [$key, [$key => false]]
        ];
    }

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    #[DataProvider('shouldPassRequiredDataProvider')]
    public function testShouldPassRequired(string $key, array $payload): void
    {
        $this->sut->required($key, $payload);
        $this->expectNotToPerformAssertions();
    }

    public static function shouldFailOptionalDataProvider(): array
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
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    #[DataProvider('shouldFailOptionalDataProvider')]
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

    public static function shouldPassOptionalDataProvider(): array
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
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws InvalidBoolValueException
     */
    #[DataProvider('shouldPassOptionalDataProvider')]
    public function testShouldPassOptional(string $key, array $payload): void
    {
        $this->sut->optional($key, $payload);
        $this->expectNotToPerformAssertions();
    }
}
