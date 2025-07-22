<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml\Product;

use HarveyNorman\Promotional\Model\ProductFactory;
use Magento\Backend\App\Action;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\HttpPostActionInterface;
use HarveyNorman\Promotional\Helper\Validation;
use HarveyNorman\Promotional\Model\Queue\ProductQueuePublisher;

class Save extends Action implements HttpPostActionInterface
{
    /**
     * @var ProductFactory
     */
    protected ProductFactory $productFactory;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var Validation
     */
    protected Validation $validationHelper;

    /**
     * @var ProductQueuePublisher
     */
    protected ProductQueuePublisher $queuePublisher;

    /**
     *
     * Save constructor.
     *
     * @param Action\Context $context
     * @param ProductFactory $productFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Validation $validationHelper
     * @param ProductQueuePublisher $queuePublisher
     */
    public function __construct(
        Action\Context $context,
        ProductFactory $productFactory,
        DataPersistorInterface $dataPersistor,
        Validation $validationHelper,
        ProductQueuePublisher $queuePublisher   
    ) {
        parent::__construct($context);
        $this->productFactory = $productFactory;
        $this->dataPersistor = $dataPersistor;
        $this->validationHelper = $validationHelper;
        $this->queuePublisher = $queuePublisher;
    }
 

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }

        // Retrieve and validate dates
        $errorMessage = $this->validationHelper->validateDates(
            $this->getRequest()->getParam('start_date'),
            $this->getRequest()->getParam('end_date')
        );

        if ($errorMessage !== null) {

            // Show the validation error to the user
            $this->messageManager->addErrorMessage(__($errorMessage));
            $this->dataPersistor->set('harveynorman_promotional_product', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }


        $id = (int) $this->getRequest()->getParam('product_id');
        $model = $this->productFactory->create();

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This product no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        }

        $model->setData($data);

        try {
            $model->save();

            //Add to queue for elasticsearch reindex
            $this->queuePublisher->publish($model);

            $this->messageManager->addSuccessMessage(__('You saved the product.'));
            $this->dataPersistor->clear('harveynorman_promotional_product');

            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['product_id' => $model->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the product.'));
        }

        $this->dataPersistor->set('harveynorman_promotional_product', $data);
        return $resultRedirect->setPath('*/*/edit', ['product_id' => $id]);
    }
}
