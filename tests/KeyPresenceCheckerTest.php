<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryForbiddenException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Service\KeyPresenceChecker;

class KeyPresenceCheckerTest extends CustomTestCase
{
    private KeyPresenceChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyPresenceChecker();
    }

    public function shouldFailRequiredPresenceDataProvider(): array
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
     * @dataProvider shouldFailRequiredPresenceDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
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

    public function shouldPassRequiredPresenceDataProvider(): array
    {
        $key = 'myKey';
        return [
            [[$key => 'blah'], $key],
            [[$key => 0], $key],
            [[$key => [[]]], $key],
        ];
    }

    /**
     * @dataProvider shouldPassRequiredPresenceDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     */
    public function testShouldPassRequiredPresence(
        array $payloadFixture,
        string $requiredKey
    ): void {
        $this->sut->required($requiredKey, $payloadFixture);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailForbiddenPresenceDataProvider(): array
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
     * @dataProvider shouldFailForbiddenPresenceDataProvider
     * @throws EntryForbiddenException
     */
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

    public function shouldPassForbiddenPresenceDataProvider(): array
    {
        $key = 'myKey';
        return [
            [[], $key],
            [[$key => null], $key]
        ];
    }

    /**
     * @dataProvider shouldPassForbiddenPresenceDataProvider
     * @throws EntryForbiddenException
     */
    public function testShouldPassForbiddenPresence(array $payloadFixture, $forbiddenKey): void
    {
        $this->sut->forbidden($forbiddenKey, $payloadFixture);
        $this->expectNotToPerformAssertions();
    }
}
