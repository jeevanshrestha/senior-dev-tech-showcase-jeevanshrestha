<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\ResourceModel\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use HarveyNorman\Promotional\Model\Product as ProductModel;
use HarveyNorman\Promotional\Model\ResourceModel\Product as ProductResourceModel;
class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'product_id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            ProductModel::class,
            ProductResourceModel::class
        );
    }
}

