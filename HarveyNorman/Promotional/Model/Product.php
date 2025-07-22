<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model;

use Magento\Framework\Model\AbstractModel;
use HarveyNorman\Promotional\Api\Data\ProductInterface;
use \HarveyNorman\Promotional\Model\ResourceModel\Product as ProductResourceModel;
use HarveyNorman\Promotional\Model\Service\DiscountedPrice;
use HarveyNorman\Promotional\Model\Service\PromotionEligibility;

class Product extends AbstractModel implements ProductInterface
{
    
    private PromotionEligibility $promotionEligibility;
    private DiscountedPrice $discountedPrice;
  
    /**
     * Define resource model
     */
    protected function _construct(): void
    {
        $this->_init(ProductResourceModel::class);
    }

    public function __construct(
        PromotionEligibility $promotionEligibility,
        DiscountedPrice $discountedPrice
    ) {
        $this->promotionEligibility = $promotionEligibility;
        $this->discountedPrice = $discountedPrice;
        parent::__construct();
    }

    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId(): int
    {
        return (int)$this->getData(self::PRODUCT_ID);
    }
    /**
     * Set product ID
     *
     * @param int $productId
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */
   
    public function setProductId(int $productId): ProductInterface
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
    /**
     * Get SKU
     *
     * @return string
     */    

    public function getSku(): string
    {
        return (string)$this->getData(self::SKU);
    }
    /**
     * Set SKU
     *
     * @param string $sku
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setSku(string $sku): ProductInterface
    {
        return $this->setData(self::SKU, $sku);
    }
    /**
     * Get product name
     *
     * @return string
     */

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }
    /**
     * Set product name
     *
     * @param string $name
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setName(string $name): ProductInterface
    {
        return $this->setData(self::NAME, $name);
    }
    /**
     * Get product price
     *
     * @return float
     */

    public function getPrice(): float
    {
        return (float)$this->getData(self::PRICE);
    }
    /**
     * Set product price
     *
     * @param float $price
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setPrice($price): ProductInterface
    {
        return $this->setData(self::PRICE, $price);
    }
    /**
     * Get promotion status
     *
     * @return bool
     */

    public function getPromotionStatus(): bool
    {
        $data = $this->getData(self::PROMOTION_STATUS);
        return $data !== null ? (bool)$data : null;
    }
    /**
     * Set promotion status
     *
     * @param bool $status
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */
    public function setPromotionStatus($status): ProductInterface
    {
        return $this->setData(self::PROMOTION_STATUS, $status);
    }
    /**
     * Get discount percentage
     *
     * @return float|null
     */

    public function getDiscountPercentage(): float
    {
        return (float)$this->getData(self::DISCOUNT_PERCENTAGE);
    }
    /**
     * Set discount percentage
     *
     * @param float $discount
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setDiscountPercentage($discount): ProductInterface
    {
        return $this->setData(self::DISCOUNT_PERCENTAGE, $discount);
    }
    /**
     * Get start date of promotion
     *
     * @return string
     */

    public function getStartDate(): string
    {
        return $this->getData(self::START_DATE);
    }
    /**
     * Set start date of promotion
     *
     * @param string $startDate
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setStartDate(string $startDate): ProductInterface
    {
        return $this->setData(self::START_DATE, $startDate);
    }


    public function getEndDate(): string
    {
        return $this->getData(self::END_DATE);
    }
    /**
     * Set end date of promotion
     *
     * @param string $endDate
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setEndDate(string $endDate): ProductInterface
    {
        return $this->setData(self::END_DATE, $endDate);
    }   
    /**
     * Get created at timestamp
     *
     * @return string
     */  

    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }
    /**
     * Set created at timestamp
     *
     * @param string $createdAt
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setCreatedAt(string $createdAt): ProductInterface
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    /**
     * Get updated at timestamp
     *
     * @return string
     */

    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }
    /**
     * Set updated at timestamp
     *
     * @param string $updatedAt
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     */

    public function setUpdatedAt(string $updatedAt): ProductInterface
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    } 

    public function isEligibleForPromotion(): bool
    {
        return $this->promotionEligibility->isEligible($this);
    }
    
    public function getDiscountedPrice(): float
    {
        return $this->discountedPrice->getDiscountedPrice($this);
    }
}