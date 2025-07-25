<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml\Product;

use HarveyNorman\Promotional\Model\ResourceModel\Product\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultInterface;
use HarveyNorman\Promotional\Model\Queue\ProductDeletePublisher;

class MassDelete extends Action
{
    /**
     * Authorization level
     */
    public const ADMIN_RESOURCE = 'HarveyNorman_Promotional::top_level';

    /**
     * @var Filter
     */
    protected Filter $filter;

    /**
     * @var CollectionFactory
     */
    protected  CollectionFactory $collectionFactory;

    private ProductDeletePublisher $productDeletePublisher;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param ProductDeletePublisher $productDeletePublisher
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductDeletePublisher $productDeletePublisher
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productDeletePublisher = $productDeletePublisher;
    }

    /**
     * Execute method
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = 0;

            foreach ($collection as $item) {
                $item->delete();
                $deleted++;

                // Add product id to Mq to delete data in Elastic Search
                $this->productDeletePublisher->publish($item);
            }

            if ($deleted) {
                $this->messageManager->addSuccessMessage(__('%1 product(s) were successfully deleted.', $deleted));
            } else {
                $this->messageManager->addNoticeMessage(__('No products were selected or deleted.'));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the products.'));
        }

        return $redirect->setPath('*/*/');
    }
}
