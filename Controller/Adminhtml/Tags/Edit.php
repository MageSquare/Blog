<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Tags;

use MageSquare\Blog\Model\DataProvider\AbstractModifier;

class Edit extends \MageSquare\Blog\Controller\Adminhtml\Tags
{
    const CURRENT_MAGESQUARE_BLOG_TAG = 'current_magesquare_blog_tag';

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store');
        $model = $this->getTagRepository()->getTagModel();

        if ($id) {
            try {
                $model = $this->getTagRepository()->getById($id);
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->_redirect('*/*');

                return;
            }
        }

        // set entered data if was error when we do save
        $data = $this->_getSession()->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->getRegistry()->register(self::CURRENT_MAGESQUARE_BLOG_TAG, $model);
        $this->getRegistry()->register(AbstractModifier::CURRENT_STORE_ID, $storeId);
        $this->initAction();
        if ($model->getId()) {
            $title = __('Edit Tag `%1`', $model->getName());
        } else {
            $title = __("Add New Tag");
        }
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_view->renderLayout();
    }

    /**
     * @return $this
     */
    private function initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('MageSquare_Blog::tags')->_addBreadcrumb(
            __('MageSquare Blog Tags'),
            __('MageSquare Blog Tags')
        );

        return $this;
    }
}
