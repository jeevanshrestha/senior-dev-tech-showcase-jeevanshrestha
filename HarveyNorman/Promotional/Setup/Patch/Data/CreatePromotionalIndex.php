<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use HarveyNorman\Promotional\Helper\ElasticsearchHelper;

class CreatePromotionalIndex implements DataPatchInterface
{
    private ElasticsearchHelper $elasticHelper;

    public function __construct(
        ElasticsearchHelper $elasticHelper
    ) {
        $this->elasticHelper = $elasticHelper;
    }

    public function apply()
    {
        $this->elasticHelper->createIndexTemplate();
        $this->elasticHelper->createIndex();
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
