<?php

declare(strict_types=1);

namespace JsonValidator\Tests;

use JsonValidator\Exception\IntegerComponentsDontRepresentDate;
use JsonValidator\Service\ValueIntegerChecker;
use PHPUnit\Framework\Attributes\DataProvider;

class ValueIntegerCheckerTest extends CustomTestCase
{
    private ValueIntegerChecker $sut;

    public function setUp(): void
    {
        parent::setUp();
        $this->sut = new ValueIntegerChecker();
    }

    public static function shouldFailIntegerGroupRepresentsADateDataProvider(): array
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
     * @throws IntegerComponentsDontRepresentDate
     */
    #[DataProvider('shouldFailIntegerGroupRepresentsADateDataProvider')]
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

    public static function shouldPassIntegerGroupRepresentsADateDataProvider(): array
    {
        return [
            [2024, 3, 27],
            [1, 2, 3] // Yes, why not. FEB 3rd, year 1
        ];
    }

    /**
     * @throws IntegerComponentsDontRepresentDate
     */
    #[DataProvider('shouldPassIntegerGroupRepresentsADateDataProvider')]
    public function testShouldPassIntegerGroupRepresentsADate(
        int $year,
        int $month,
        int $day
    ): void {
        $this->sut->integerGroupRepresentsADate($year, $month, $day);
        $this->expectNotToPerformAssertions();
    }
}
