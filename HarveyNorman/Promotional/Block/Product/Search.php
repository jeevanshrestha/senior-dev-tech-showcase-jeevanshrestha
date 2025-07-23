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
        protected CollectionFactory $productCollectionFactory,
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
            return $this->productCollectionFactory->create()->addFieldToFilter('product_id', -1); // Return empty
        }

        $ids = $this->elasticHelper->search($this->searchQuery, 100);
        if (empty($ids)) {
            return $this->productCollectionFactory->create()->addFieldToFilter('product_id', -1); // Return empty
        }

        $now = (new \DateTime())->format('Y-m-d H:i:s');
        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('product_id', ['in' => $ids])
            ->addFieldToFilter('promotion_status', 1)
            ->addFieldToFilter('start_date', ['lteq' => $now])
            ->addFieldToFilter('end_date', ['gteq' => $now]);

        $collection->load();
        return $collection;
    }
}