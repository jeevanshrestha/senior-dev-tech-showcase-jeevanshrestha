<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;

class MassStatus extends Action
{
    public const ADMIN_RESOURCE = 'HarveyNorman_Promotional::product';

    protected CollectionFactory $collectionFactory;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('selected');
        $status = $this->getRequest()->getParam('status');

        if (!is_array($ids) || $status === null) {
            $this->messageManager->addErrorMessage(__('Please select items and a status.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
        }

        $collection = $this->collectionFactory->create()->addFieldToFilter('product_id', ['in' => $ids]);

        foreach ($collection as $item) {
            $item->setData('promotion_status', (int) $status);
            $item->save();
        }

        $this->messageManager->addSuccessMessage(__('Updated status for %1 item(s).', count($ids)));
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
