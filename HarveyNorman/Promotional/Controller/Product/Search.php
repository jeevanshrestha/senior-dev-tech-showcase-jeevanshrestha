<?php
declare(strict_types=1);

namespace HarveyNorman\Promotional\Controller\Product;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Search extends Action
{
    protected PageFactory $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }


    public function execute()
    {
        $searchTerm = (string) $this->getRequest()->getParam('q');

        $resultPage = $this->pageFactory->create();
        $block = $resultPage->getLayout()->getBlock('promotional.product.search');
        if ($block) {
            $block->setSearchQuery($searchTerm);
        }
        return $resultPage;
    }
}
