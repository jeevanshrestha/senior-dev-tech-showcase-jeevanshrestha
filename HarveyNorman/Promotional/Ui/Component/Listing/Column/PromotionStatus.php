<?php
namespace HarveyNorman\Promotional\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class PromotionStatus extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }
        foreach ($dataSource['data']['items'] as &$item) {
            if (isset($item['promotion_status'])) {
                if ((int)$item['promotion_status'] === 1) {
                    $item['promotion_status'] = '<span style="background-color: #28a745; color: white; padding: 3px 7px; border-radius: 3px;">Enabled</span>';
                } else {
                    $item['promotion_status'] = '<span style="background-color: #ba3545; color: white; padding: 3px 7px; border-radius: 3px;">Disabled</span>';
                }
            }
        }
        return $dataSource;
    }
}
