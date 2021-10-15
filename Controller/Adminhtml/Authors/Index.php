<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Authors;

/**
 * Class
 */
class Index extends \MageSquare\Blog\Controller\Adminhtml\Authors
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->getPageFactory()->create();
        $resultPage->setActiveMenu('MageSquare_Blog::authors');
        $resultPage->getConfig()->getTitle()->prepend(__('Authors'));
        $resultPage->addBreadcrumb(__('Authors'), __('Authors'));

        return $resultPage;
    }
}
