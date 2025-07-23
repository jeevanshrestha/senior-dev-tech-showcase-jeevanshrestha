<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Block\Product;

use Magento\Framework\View\Element\Template;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;

class Index extends Template
{
    protected CollectionFactory $productCollectionFactory;


    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;

//
    }

    /**
     * Get paginated promotional products
     *
     * @return \HarveyNorman\Promotional\Model\ResourceModel\Product\Collection
     */
    public function getProducts()
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $collection = $this->productCollectionFactory->create()
            ->addFieldToFilter('promotion_status', 1)
            ->addFieldToFilter('start_date', ['lteq' => $now])
            ->addFieldToFilter('end_date', ['gteq' => $now]);

        $collection->load();
        return $collection;
    }

}
