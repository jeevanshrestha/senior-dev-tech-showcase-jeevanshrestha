<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml\Product;

use HarveyNorman\Promotional\Controller\Adminhtml\Product;
use HarveyNorman\Promotional\Model\ProductFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

class Delete extends Product implements HttpPostActionInterface
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context  
     * @param \Magento\Framework\Registry $coreRegistry     
     * @param ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context, 
        \Magento\Framework\Registry $coreRegistry,
        ProductFactory $productFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->productFactory = $productFactory;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('product_id');

        if ($id) {
            try {
                $product = $this->productFactory->create()->load($id);

                if (!$product->getId()) {
                    throw new LocalizedException(__('Product no longer exists.'));
                }

                $product->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the product.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while deleting the product.'));
            }

            return $resultRedirect->setPath('*/*/edit', ['product_id' => $id]);
        }

        $this->messageManager->addErrorMessage(__('We can\'t find a product to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
