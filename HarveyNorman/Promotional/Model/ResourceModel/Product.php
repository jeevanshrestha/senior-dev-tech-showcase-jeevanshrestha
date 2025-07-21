<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Product extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('harveynorman_promotional_product', 'product_id');
    }
}

