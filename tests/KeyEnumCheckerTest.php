<?php

declare(strict_types=1);

namespace Tests\JsonValidator;

use PHPUnit\Framework\TestCase;
use JsonValidator\Exception\EntryEmptyException;
use JsonValidator\Exception\EntryMissingException;
use JsonValidator\Exception\JsonPayloadValidatorUnmanagedException;
use JsonValidator\Exception\ValueNotInListException;
use JsonValidator\Service\KeyEnumChecker;
use JsonValidator\Service\KeyPresenceChecker;

class KeyEnumCheckerTest extends TestCase
{
    private KeyEnumChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new KeyEnumChecker(new KeyPresenceChecker());
    }

    public function shouldPassValidationDataProvider(): array
    {
        $key = 'myKey';
        $validValues1 = [1, 2, 3];
        $validValues2 = ['a', 'b', 'c'];

        // Case sensitive, mixed types... not that I recommend using enums this way, but you can
        $validValues3 = ["A", 3, "b", 3.33, true, false];

        $fixedTests = [
            // Not required, so it doesn't matter that the key isn't there
            [$key, [], $validValues1, false],
            [$key, [$key => null], $validValues1, false],
        ];

        $variable = [true, false];

        $variableTests = [];

        foreach ($variable as $v) {
            $variableTests[] = [$key, [$key => 3], $validValues1, $v];
            $variableTests[] = [$key, [$key => 'a'], $validValues2, $v];
            $variableTests[] = [$key, [$key => 'A'], $validValues3, $v];
            $variableTests[] = [$key, [$key => 3], $validValues3, $v];
            $variableTests[] = [$key, [$key => "b"], $validValues3, $v];
            $variableTests[] = [$key, [$key => 3.33], $validValues3, $v];
            $variableTests[] = [$key, [$key => true], $validValues3, $v];
            $variableTests[] = [$key, [$key => false], $validValues3, $v];
        }

        return array_merge($fixedTests, $variableTests);
    }

    /**
     * @dataProvider shouldPassValidationDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotInListException
     * @throws JsonPayloadValidatorUnmanagedException
     */
    public function testShouldPassValidation(string $key, array $payload, array $validValues, bool $required): void
    {
        $this->sut->isEnum($key, $payload, $validValues, $required);
        $this->expectNotToPerformAssertions();
    }

    public function shouldFailValidationDataProvider(): array
    {
        $key = 'myKey';
        $validValues1 = [1, 2, 3];
        $validValues2 = ['a', 'b', 'c'];

        $m1 = "Entry 'myKey' missing";
        $m2 = "The key 'myKey' can only be one of the following: [1 | 2 | 3], but it is '6'";
        $m3 = "The key 'myKey' can only be one of the following: [1 | 2 | 3], but it is '3'";
        $m4 = "Entry 'myKey' empty";
        $m5 = "The key 'myKey' can only be one of the following: [1 | 2 | 3], but it is '[[]]'";
        $m6 = "The key 'myKey' can only be one of the following: [1 | 2 | 3], but it is '2.25'";
        $m7 = "The key 'myKey' can only be one of the following: [a | b | c], but it is 'A'";

        $fixedTests = [
            // Required but missing
            [$key, [], $validValues1, true, EntryMissingException::class, $m1],
            [$key, [$key => null], $validValues1, true, EntryEmptyException::class, $m4]
        ];

        $variableTests = [];

        $variables = [true, false];

        foreach ($variables as $v) {
            $variableTests[] = [$key, [$key => 6], $validValues1, $v, ValueNotInListException::class, $m2];
            $variableTests[] = [$key, [$key => "3"], $validValues1, $v, ValueNotInListException::class, $m3];
            $variableTests[] = [$key, [$key => [[]]], $validValues1, $v, ValueNotInListException::class, $m5];
            $variableTests[] = [$key, [$key => 2.25], $validValues1, $v, ValueNotInListException::class, $m6];

            // Case sensitive
            $variableTests[] = [$key, [$key => "A"], $validValues2, $v, ValueNotInListException::class, $m7];
        }

        return array_merge($variableTests, $fixedTests);
    }

    /**
     * @dataProvider shouldFailValidationDataProvider
     * @throws EntryEmptyException
     * @throws EntryMissingException
     * @throws ValueNotInListException
     * @throws JsonPayloadValidatorUnmanagedException
     */
    public function testShouldFailValidation(
        string $key,
        array $payload,
        array $validValues,
        bool $required,
        string $expectedException,
        string $expectedMessage
    ): void {
        $this->expectException($expectedException);
        $this->expectExceptionMessage($expectedMessage);
        $this->sut->isEnum($key, $payload, $validValues, $required);
    }
}
