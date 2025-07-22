<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Test\Unit\Model\Service;

use DateTime;
use DateTimeZone;
use HarveyNorman\Promotional\Model\Service\PromotionEligibility;
use HarveyNorman\Promotional\Api\Data\ProductInterface;
use PHPUnit\Framework\TestCase;

class PromotionEligibilityTest extends TestCase
{
    private ProductInterface $productMock;

    protected function setUp(): void
    {
        $this->productMock = $this->createMock(ProductInterface::class);
    }

    public function testIsEligibleReturnsTrueWhenStatusIsOneAndDateInRange(): void
    {
        $now = new DateTime('2025-07-22 12:00:00', new DateTimeZone('UTC'));

        $this->productMock->method('getPromotionStatus')->willReturn(true);
        $this->productMock->method('getStartDate')->willReturn('2025-07-20 00:00:00');
        $this->productMock->method('getEndDate')->willReturn('2025-07-25 23:59:59');

        $eligibility = new PromotionEligibility(new DateTimeZone('UTC'), $now);
        $this->assertTrue($eligibility->isEligible($this->productMock));
    }

    public function testIsEligibleReturnsFalseWhenStatusIsNotOne(): void
    {
        $this->productMock->method('getPromotionStatus')->willReturn(false);

        $eligibility = new PromotionEligibility();
        $this->assertFalse($eligibility->isEligible($this->productMock));
    }

    public function testIsEligibleReturnsFalseWhenDatesAreMissing(): void
    {
        $this->productMock->method('getPromotionStatus')->willReturn(true);
        $this->productMock->method('getStartDate')->willReturn('');
        $this->productMock->method('getEndDate')->willReturn('');

        $eligibility = new PromotionEligibility();
        $this->assertFalse($eligibility->isEligible($this->productMock));
    }

    public function testIsEligibleReturnsFalseWhenCurrentDateIsBeforeStart(): void
    {
        $now = new DateTime('2025-07-10 12:00:00', new DateTimeZone('UTC'));

        $this->productMock->method('getPromotionStatus')->willReturn(true);
        $this->productMock->method('getStartDate')->willReturn('2025-07-20 00:00:00');
        $this->productMock->method('getEndDate')->willReturn('2025-07-25 23:59:59');

        $eligibility = new PromotionEligibility(new DateTimeZone('UTC'), $now);
        $this->assertFalse($eligibility->isEligible($this->productMock));
    }

    public function testIsEligibleReturnsFalseWhenCurrentDateIsAfterEnd(): void
    {
        $now = new DateTime('2025-07-30 12:00:00', new DateTimeZone('UTC'));

        $this->productMock->method('getPromotionStatus')->willReturn(true);
        $this->productMock->method('getStartDate')->willReturn('2025-07-20 00:00:00');
        $this->productMock->method('getEndDate')->willReturn('2025-07-25 23:59:59');

        $eligibility = new PromotionEligibility(new DateTimeZone('UTC'), $now);
        $this->assertFalse($eligibility->isEligible($this->productMock));
    }

    public function testIsEligibleReturnsFalseWhenDateFormatIsInvalid(): void
    {
        $this->productMock->method('getPromotionStatus')->willReturn(true);
        $this->productMock->method('getStartDate')->willReturn('invalid-date');
        $this->productMock->method('getEndDate')->willReturn('also-invalid');

        $eligibility = new PromotionEligibility();
        $this->assertFalse($eligibility->isEligible($this->productMock));
    }
}
