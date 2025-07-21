<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Api;

use \HarveyNorman\Promotional\Api\Data\ProductInterface;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchResultsInterface;
/**
 * Interface for product repository.
 */
interface ProductRepositoryInterface
{
    /**
     * Save product
     *
     * @param \HarveyNorman\Promotional\Api\Data\ProductInterface $product
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(ProductInterface $product): ProductInterface;

    /**
     * Get product by ID
     *
     * @param int $productId
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $productId): ProductInterface;

    /**
     * Delete product
     *
     * @param \HarveyNorman\Promotional\Api\Data\ProductInterface $product
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(ProductInterface $product): bool;

    /**
     * Retrieve products matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList( SearchCriteriaInterface $searchCriteria): SearchResultsInterface;
}