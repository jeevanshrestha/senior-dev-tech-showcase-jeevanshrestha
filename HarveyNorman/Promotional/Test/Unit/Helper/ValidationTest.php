<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Test\Unit\Helper;

use HarveyNorman\Promotional\Helper\Validation;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use PHPUnit\Framework\TestCase;

class ValidationTest extends TestCase
{
    /**
     * @var DateTime|\PHPUnit\Framework\MockObject\MockObject
     */
    private $dateTimeMock;

    /**
     * @var Validation
     */
    private $validation;

    protected function setUp(): void
    {
        $this->dateTimeMock = $this->createMock(DateTime::class);
        $this->validation = new Validation($this->dateTimeMock);
    }

    public function testValidateDatesThrowsExceptionWhenStartDateOrEndDateIsEmpty(): void
    {
        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Start Date and End Date cannot be empty.');

        $this->validation->validateDates(null, '2025-07-22 12:00:00');
    }

    public function testValidateDatesThrowsExceptionWhenEndDateIsBeforeStartDate(): void
    {
        $startDate = '2025-07-22 12:00:00';
        $endDate = '2025-07-21 12:00:00';

        $this->dateTimeMock->method('timestamp')
            ->willReturnMap([
                [$startDate, 1721640000],
                [$endDate, 1721553600],
            ]);

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('End Date must be greater than or equal to Start Date.');

        $this->validation->validateDates($startDate, $endDate);
    }

    public function testValidateDatesSucceedsWhenEndDateIsAfterOrEqualStartDate(): void
    {
        $startDate = '2025-07-22 12:00:00';
        $endDate = '2025-07-23 12:00:00';

        $this->dateTimeMock->method('timestamp')
            ->willReturnMap([
                [$startDate, 1721640000],
                [$endDate, 1721726400],
            ]);

        // No exception should be thrown
        $this->validation->validateDates($startDate, $endDate);
        $this->addToAssertionCount(1); // Confirm it passed
    }
}
