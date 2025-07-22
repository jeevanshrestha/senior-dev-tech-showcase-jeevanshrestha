<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\Service;

use HarveyNorman\Promotional\Model\Service\PromotionEligibility;
use HarveyNorman\Promotional\Api\Data\ProductInterface;
use HarveyNorman\Promotional\Model\Product;
class DiscountedPrice
{
    /**
     * @var PromotionEligibility
     */
    private $promotionEligibility;

    /**
     * DiscountedPrice constructor.
     *
     * @param PromotionEligibility $promotionEligibility
     */
    public function __construct(PromotionEligibility $promotionEligibility)
    {
        $this->promotionEligibility = $promotionEligibility;
    }

    /**
     * Get discounted price if eligible for promotion.
     *
     * @param Product $product
     * @return float|null
     */
    public function getDiscountedPrice(ProductInterface $product): float
    {
        $originalPrice = $product->getPrice();

        if ($this->promotionEligibility->isEligible($product)) {
            
            $discountPercentage = $product->getDiscountPercentage();

            if ($discountPercentage) {
                $discountAmount = ($originalPrice * (float)$discountPercentage) / 100;
                return $originalPrice - $discountAmount;
            }

            
        }
        return $originalPrice; // No discount percentage, return original price
    }
}