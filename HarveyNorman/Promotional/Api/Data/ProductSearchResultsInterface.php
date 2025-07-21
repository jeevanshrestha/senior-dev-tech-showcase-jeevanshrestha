<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Api\Data;

use \Magento\Framework\Api\SearchResultsInterface; 
/**
 * Interface for product search results.
 */
interface ProductSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Product list.
     * @return \HarveyNorman\Promotional\Api\Data\ProductInterface[]
     */
    public function getItems(): array;

    /**
     * Set sku list.
     * @param \HarveyNorman\Promotional\Api\Data\ProductInterface[] $items
     * @return $this
     */
    public function setItems(array $items): self;
}

