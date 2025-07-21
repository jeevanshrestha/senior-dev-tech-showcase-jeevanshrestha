<?php
namespace HarveyNorman\Promotional\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 0, 'label' => __('Disabled')],
            ['value' => 1, 'label' => __('Enabled')],
        ];
    }

    /**
     * Return key-value options (optional, for other usage)
     *
     * @return array
     */
    public function getOptions(): array
    {
        return [

            0 => __('Disabled'),
            1 => __('Enabled'),
        ];
    }
}
