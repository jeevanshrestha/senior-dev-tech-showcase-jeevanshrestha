<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml;

use Magento\Backend\App\Action;
abstract class Product extends  Action
{

    const ADMIN_RESOURCE = 'HarveyNorman_Promotional::top_level';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context
    ) {
        parent::__construct($context);
    } 

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('HarveyNorman'), __('HarveyNorman'))
            ->addBreadcrumb(__('Product'), __('Product'));
        return $resultPage;
    }
}

