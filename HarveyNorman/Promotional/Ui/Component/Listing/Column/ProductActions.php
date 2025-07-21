<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class ProductActions extends Column
{
    public const URL_PATH_EDIT = 'harveynorman_promotional/product/edit';
    public const URL_PATH_DELETE = 'harveynorman_promotional/product/delete';
    private const FIELD_ID = 'product_id';

    /**
     * @var UrlInterface
     */         
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare the data source for the product actions column.
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[self::FIELD_ID])) {
                    $name = $this->getData('name') ?? 'actions';
                    $title = $item['name'] ?? __('this product');

                    $item[$name] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                self::URL_PATH_EDIT,
                                [self::FIELD_ID => $item[self::FIELD_ID]]
                            ),
                            'label' => __('Edit'),
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                self::URL_PATH_DELETE,
                                [self::FIELD_ID => $item[self::FIELD_ID]]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete "%1"', $title),
                                'message' => __('Are you sure you want to delete "%1"?', $title),
                            ],
                            'post' => true,
                        ],
                    ];
                }
            }
        }

        return $dataSource;
    }
}
