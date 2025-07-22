<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Block\Product;

use Magento\Framework\View\Element\Template;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;
use HarveyNorman\Promotional\Helper\ElasticsearchHelper;
class Search extends Template
{
    protected ?string $searchQuery = null;

    public function __construct(
        Template\Context $context,
        protected ElasticsearchHelper $elasticHelper,
        protected CollectionFactory $collectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function setSearchQuery(string $query): void
    {
        $this->searchQuery = trim($query);
    }

    public function getProducts()
    {
        if (!$this->searchQuery) {
            return $this->collectionFactory->create()->addFieldToFilter('product_id', -1); // Return empty
        }

        $ids = $this->elasticHelper->search($this->searchQuery, 100);
        if (empty($ids)) {
            return $this->collectionFactory->create()->addFieldToFilter('product_id', -1); // Return empty
        }

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('product_id', ['in' => $ids]);

        return $collection;
    }
}