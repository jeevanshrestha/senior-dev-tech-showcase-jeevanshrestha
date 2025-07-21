<?php
declare(strict_types=1);
namespace HarveyNorman\Promotional\Api\Data;

interface ProductInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{



    const PRODUCT_ID = 'product_id';
    const SKU = 'sku';
    const NAME = 'name';
    const PRICE = 'price';
    const PROMOTION_STATUS = 'promotion_status';
    const DISCOUNT_PERCENTAGE = 'discount_percentage';
    const START_DATE = 'start_date';
    const END_DATE = 'end_date';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    /**
     * Get product ID
     *
     * @return int
     */
    public function getProductId(): int;
    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku(): string;
    
    /**
     * Get product name
     *
     * @return string
     */
    public function getName(): string;
    /**
     * Get product price
     *
     * @return float
     */
    public function getPrice(): float;
    /**
     * Get promotion status
     *
     * @return bool
     */
    public function getPromotionStatus(): bool;
    /**
     * Get discount percentage
     *
     * @return float|null
     */
    public function getDiscountPercentage():?float;
    /**
     * Get start date of promotion
     *
     * @return string
     */
    public function getStartDate(): string;
    /**
     * Get end date of promotion
     *
     * @return string
     */
    public function getEndDate(): string;
    /**
     * Get created at timestamp
     *
     * @return string
     */
    public function getCreatedAt(): string;
    /**
     * Get updated at timestamp
     *
     * @return string
     */
    public function getUpdatedAt(): string;
 
    /**
     * Set product ID
     *
     * @param int $productId
     * @return self
     */
    public function setProductId(int $productId): self;
    /**
     * Set SKU
     *
     * @param string $sku
     * @return self
     */
    public function setSku(string $sku): self;
    /**
     * Set product name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self;
    /**
     * Set product price
     *
     * @param string $price
     * @return self
     */
    public function setPrice(string $price): self;
    /**
     * Set promotion status
     *
     * @param string $promotionStatus
     * @return self
     */
    public function setPromotionStatus(string $promotionStatus): self;
    /**
     * Set discount percentage
     *
     * @param string $discountPercentage
     * @return self
     */
    public function setDiscountPercentage(string $discountPercentage): self;
    /**
     * Set start date of promotion
     *
     * @param string $startDate
     * @return self
     */
    public function setStartDate(string $startDate): self;
    
    /**
     * Set end date of promotion
     *
     * @param string $endDate
     * @return self
     */                             
    public function setEndDate(string $endDate): self;
    /**
     * Set created at timestamp
     *
     * @param string $createdAt
     * @return self
     */
    public function setCreatedAt(string $createdAt): self;
    /**
     * Set updated at timestamp
     *
     * @param string $updatedAt
     * @return self
     */
    public function setUpdatedAt(string $updatedAt): self;


}