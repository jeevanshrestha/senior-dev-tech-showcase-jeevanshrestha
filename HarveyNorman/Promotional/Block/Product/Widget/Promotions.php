<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Block\Product\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use HarveyNorman\Promotional\Helper\ElasticsearchHelper;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;

class Promotions extends Template implements BlockInterface
{
    protected $_template = 'HarveyNorman_Promotional::widget/promotions.phtml';

    public function __construct(
        Template\Context $context,
        protected ElasticsearchHelper $elasticHelper,
        protected CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getTitle(): string
    {
        return $this->getData('title') ?? 'Promotional Products';
    }

    public function getProducts()
    {
        $size = (int)($this->getData('products_count') ?? 5);
        $collection = $this->productCollectionFactory->create();
        $collection->setPageSize($size);

        $collection->load();
        return $collection;
    }
}
