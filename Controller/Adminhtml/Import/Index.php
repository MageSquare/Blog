<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Import;

use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Index
 */
class Index extends \MageSquare\Blog\Controller\Adminhtml\Import
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('MageSquare_Blog::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        $this->messageManager->addNoticeMessage(
            __('Import depends on cron, thus pressing the import button creates a cron task which will'
                . ' be fulfilled within some period of time')
        );

        return $resultPage;
    }
}
