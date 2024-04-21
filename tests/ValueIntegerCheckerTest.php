<?php

declare(strict_types=1);

namespace Tests\JsonValidator;

use PHPUnit\Framework\TestCase;
use JsonValidator\Exception\IntegerComponentsDontRepresentDate;
use JsonValidator\Service\ValueIntegerChecker;

class ValueIntegerCheckerTest extends TestCase
{
    private ValueIntegerChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ValueIntegerChecker();
    }

    public function shouldFailIntegerGroupRepresentsADateDataProvider(): array
    {
        return [
            [-1, 2, 3],
            [1, -2, 3],
            [1, 2, -3],
            [0, 0, 0],

            // Looks like a proper date, but FEB-2024 has only 29 days. Still, the PHP DateTime
            // library is able to create a date, 2024-03-01, but that doesn't match the
            // numbers we passed to the function
            [2024, 2, 30],

            [2024, 13, 28],
        ];
    }

    /**
     * @dataProvider shouldFailIntegerGroupRepresentsADateDataProvider
     * @throws IntegerComponentsDontRepresentDate
     */
    public function testShouldFailIntegerGroupRepresentsADate(
        int $year,
        int $month,
        int $day
    ): void {
        $parametrizedMessage = "Cannot construct a date with year '$year', month '$month' and day '$day'";
        $this->expectException(IntegerComponentsDontRepresentDate::class);
        $this->expectExceptionMessage($parametrizedMessage);

        $this->sut->integerGroupRepresentsADate($year, $month, $day);
    }

    public function shouldPassIntegerGroupRepresentsADateDataProvider(): array
    {
        return [
            [2024, 3, 27],
            [1, 2, 3] // Yes, why not. FEB 3rd, year 1
        ];
    }

    /**
     * @dataProvider shouldPassIntegerGroupRepresentsADateDataProvider
     * @throws IntegerComponentsDontRepresentDate
     */
    public function testShouldPassIntegerGroupRepresentsADate(
        int $year,
        int $month,
        int $day
    ): void {
        $this->sut->integerGroupRepresentsADate($year, $month, $day);
        $this->expectNotToPerformAssertions();
    }
}
