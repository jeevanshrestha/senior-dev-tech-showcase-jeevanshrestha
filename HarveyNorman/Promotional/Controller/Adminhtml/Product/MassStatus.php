<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;
use HarveyNorman\Promotional\Model\Queue\ProductQueuePublisher;

class MassStatus extends Action
{
    public const ADMIN_RESOURCE = 'HarveyNorman_Promotional::product';

    protected CollectionFactory $collectionFactory;
    private ProductQueuePublisher $queuePublisher;

    public function __construct(
        Action\Context $context,
        CollectionFactory $collectionFactory,
        ProductQueuePublisher $queuePublisher
    ) {
        parent::__construct($context);
        $this->collectionFactory = $collectionFactory;
        $this->queuePublisher = $queuePublisher;
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
        $successCount = 0;

        foreach ($collection as $promoProduct) {
            try {
                $promoProduct->setData('promotion_status', (int) $status);
                $promoProduct->save();

                //Add to queue for elasticsearch reindex
                $this->queuePublisher->publish($promoProduct);

                $successCount++;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Failed to queue product ID %1: %2', $promoProduct->getId(), $e->getMessage())
                );
            }
        }

        if ($successCount) {
            $this->messageManager->addSuccessMessage(__('Updated and queued %1 item(s) for Elasticsearch.', $successCount));
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
