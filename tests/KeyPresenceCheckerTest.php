<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Service\KeyPresenceChecker;
use PHPUnit\Framework\Attributes\DataProvider;

class KeyPresenceCheckerTest extends CustomTestCase
{
    private KeyPresenceChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyPresenceChecker();
    }

    public static function shouldFailRequiredPresenceDataProvider(): array
    {
        $key = 'myKey';

        return [
            [[], $key, EntryMissingException::class, "Entry 'myKey' missing"],
            [['otherKey' => 'value'], $key, EntryMissingException::class, "Entry 'myKey' missing"],
            [[$key => null], $key, EntryEmptyException::class, "Entry 'myKey' empty"],
            [[$key => ''], $key, EntryEmptyException::class, "Entry 'myKey' empty"],
            [[$key => '    '], $key, EntryEmptyException::class, "Entry 'myKey' empty"],
            [[$key => []], $key, EntryEmptyException::class, "Entry 'myKey' empty"],
        ];
    }

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    #[DataProvider('shouldFailRequiredPresenceDataProvider')]
    public function testShouldFailRequiredPresence(
        array $payloadFixture,
        string $requiredKey,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->sut->required($requiredKey, $payloadFixture);
    }

    public static function shouldPassRequiredPresenceDataProvider(): array
    {
        $key = 'myKey';
        return [
            [[$key => 'blah'], $key],
            [[$key => 0], $key],
            [[$key => [[]]], $key],
        ];
    }

    /**
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    #[DataProvider('shouldPassRequiredPresenceDataProvider')]
    public function testShouldPassRequiredPresence(
        array $payloadFixture,
        string $requiredKey
    ): void {
        $this->sut->required($requiredKey, $payloadFixture);
        $this->expectNotToPerformAssertions();
    }

    public static function shouldFailForbiddenPresenceDataProvider(): array
    {
        $key = 'myKey';
        $msg = "Entry 'myKey' should not be present in the payload";
        return [
            [[$key => ''], $key, EntryForbiddenException::class, $msg],
            [[$key => '   '], $key, EntryForbiddenException::class, $msg],
            [[$key => []], $key, EntryForbiddenException::class, $msg],
            [[$key => [[]]], $key, EntryForbiddenException::class, $msg],
            [[$key => 0], $key, EntryForbiddenException::class, $msg],
            [[$key => 0.0], $key, EntryForbiddenException::class, $msg]
        ];
    }

    /**
     * @throws EntryForbiddenException
     */
    #[DataProvider('shouldFailForbiddenPresenceDataProvider')]
    public function testShouldFailForbiddenPresence(
        array $payloadFixture,
        string $forbiddenKey,
        string $expectedExceptionClass,
        string $expectedExceptionMessage
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->sut->forbidden($forbiddenKey, $payloadFixture);
    }

    public static function shouldPassForbiddenPresenceDataProvider(): array
    {
        $key = 'myKey';
        return [
            [[], $key],
            [[$key => null], $key]
        ];
    }

    /**
     * @throws EntryForbiddenException
     */
    #[DataProvider('shouldPassForbiddenPresenceDataProvider')]
    public function testShouldPassForbiddenPresence(array $payloadFixture, $forbiddenKey): void
    {
        $this->sut->forbidden($forbiddenKey, $payloadFixture);
        $this->expectNotToPerformAssertions();
    }
}
