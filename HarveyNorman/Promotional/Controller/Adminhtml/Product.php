<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\Page as ResultPage;

abstract class Product extends Action
{
    public const ADMIN_RESOURCE = 'HarveyNorman_Promotional::top_level';

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @param Action\Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Action\Context $context,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Init page
     *
     * @param ResultPage $resultPage
     * @return ResultPage
     */
    public function initPage(ResultPage $resultPage): ResultPage
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('HarveyNorman'), __('HarveyNorman'))
            ->addBreadcrumb(__('Promotional Products'), __('Promotional Products'));

        return $resultPage;
    }
}
