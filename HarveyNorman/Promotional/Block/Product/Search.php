<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Block\Product;

use Magento\Framework\View\Element\Template;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;

class Search extends Template
{
    protected CollectionFactory $productCollectionFactory;

    protected ?int $pageSize = null;
    protected ?int $currentPage = null;

    public function __construct(
        Template\Context $context,
        CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productCollectionFactory = $productCollectionFactory;

        $this->pageSize = isset($data['page_size']) ? (int)$data['page_size'] : 20;
        $this->currentPage = (int)$this->getRequest()->getParam('p', 1);
    }

    /**
     * Get paginated promotional products
     *
     * @return \HarveyNorman\Promotional\Model\ResourceModel\Product\Collection
     */
    public function getProducts()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->setPageSize($this->pageSize);
        $collection->setCurPage((int)$this->currentPage);

        // Assign collection to pager child block
        if ($pager = $this->getChildBlock('pager')) {
            $pager->setLimit($this->pageSize)
                ->setCollection($collection);
        }

        $collection->load();

        return $collection;
    }

}
