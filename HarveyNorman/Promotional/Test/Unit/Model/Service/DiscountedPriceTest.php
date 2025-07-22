<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Test\Unit\Model\Service;

use PHPUnit\Framework\TestCase;
use HarveyNorman\Promotional\Model\Service\DiscountedPrice;
use HarveyNorman\Promotional\Model\Service\PromotionEligibility;
use HarveyNorman\Promotional\Api\Data\ProductInterface;

class DiscountedPriceTest extends TestCase
{
    /**
     * @var PromotionEligibility|\PHPUnit\Framework\MockObject\MockObject
     */
    private $promotionEligibilityMock;

    /**
     * @var DiscountedPrice
     */
    private $discountedPriceService;

    protected function setUp(): void
    {
        $this->promotionEligibilityMock = $this->getMockBuilder(PromotionEligibility::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->discountedPriceService = new DiscountedPrice($this->promotionEligibilityMock);
    }

    public function testGetDiscountedPriceWithEligibility(): void
    {
        $productMock = $this->createMock(ProductInterface::class);

        $productMock->method('getPrice')->willReturn(200.0);
        $productMock->method('getDiscountPercentage')->willReturn(10.0);

        $this->promotionEligibilityMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($productMock)
            ->willReturn(true);

        $discounted = $this->discountedPriceService->getDiscountedPrice($productMock);

        $this->assertEquals(180.0, $discounted);
    }

    public function testGetDiscountedPriceWithoutEligibility(): void
    {
        $productMock = $this->createMock(ProductInterface::class);

        $productMock->method('getPrice')->willReturn(150.0);

        $this->promotionEligibilityMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($productMock)
            ->willReturn(false);

        $discounted = $this->discountedPriceService->getDiscountedPrice($productMock);

        $this->assertEquals(150.0, $discounted);
    }

    public function testGetDiscountedPriceWithZeroDiscount(): void
    {
        $productMock = $this->createMock(ProductInterface::class);

        $productMock->method('getPrice')->willReturn(300.0);
        $productMock->method('getDiscountPercentage')->willReturn(null);

        $this->promotionEligibilityMock
            ->expects($this->once())
            ->method('isEligible')
            ->with($productMock)
            ->willReturn(true);

        $discounted = $this->discountedPriceService->getDiscountedPrice($productMock);

        $this->assertEquals(300.0, $discounted);
    }
}
