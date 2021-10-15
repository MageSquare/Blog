<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Controller\Adminhtml\Categories;

use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Block\Sidebar\Category\TreeRenderer;
use MageSquare\Blog\Model\Categories;
use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Controller\Adminhtml\Traits\SaveTrait;

class Save extends \MageSquare\Blog\Controller\Adminhtml\Categories
{
    use SaveTrait;

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = (int)$this->getRequest()->getParam(CategoryInterface::CATEGORY_ID);

            try {
                $inputFilter = new \Zend_Filter_Input([], [], $data);
                $data = $inputFilter->getUnescaped();
                $data[CategoryInterface::STORE_ID] = $data[CategoryInterface::STORE_ID] ?: 0;

                if ($id) {
                    $model = $this->getCategoryRepository()->getById($id);
                    $data = $this->retrieveItemContent($data, $model);
                } else {
                    $model = $this->getCategoryRepository()->getCategory();
                }

                $model->addData($data);

                if ($this->isUrlKeyExisted($model->getUrlKey(), $model->getCategoryId())) {
                    $this->getDataPersistor()->set(Categories::PERSISTENT_NAME, $data);
                    $this->getMessageManager()->addErrorMessage(__('Category with the same url key already exists'));
                    $this->addRedirect($id);

                    return;
                }

                if ($model->getParentId() && $model->getParentCategory()->getLevel() + 1 > TreeRenderer::LEVEL_LIMIT) {
                    $this->getMessageManager()->addErrorMessage(
                        __(
                            'You have exceeded the category tree depth which is limited by %1.',
                            TreeRenderer::LEVEL_LIMIT
                        )
                    );
                    $this->addRedirect($id);

                    return;
                }

                $this->_getSession()->setPageData($model->getData());
                $urlKeyUseDefault = $this->getUseDefaults()[CategoryInterface::URL_KEY] ?? false;

                if (!$model->getUrlKey() && !$urlKeyUseDefault) {
                    $model->setUrlKey($this->getUrlHelper()->generate($model->getName()));
                }

                $this->getCategoryRepository()->save($model);
                $this->getMessageManager()->addSuccessMessage(__('You saved the item.'));
                $this->_getSession()->setPageData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', [
                        'id' => $model->getCategoryId(),
                        'store' => (int)$this->getRequest()->getParam('store_id', 0)
                    ]);

                    return;
                }

                $this->_redirect('*/*/');

                return;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->getDataPersistor()->set(Categories::PERSISTENT_NAME, $data);
                $this->addRedirect($id);

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->getMessageManager()->addErrorMessage($e->getMessage());
                $this->_getSession()->setPageData(null);
                $this->getDataPersistor()->set(Categories::PERSISTENT_NAME, $data);
                $this->addRedirect($id);

                return;
            } catch (\Exception $e) {
                $this->getMessageManager()->addErrorMessage(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->getLogger()->critical($e);
                $this->_getSession()->setPageData($data);
                $this->_redirect('*/*/edit', ['category_id' => $id]);

                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * @return array
     */
    private function getFieldsByStore()
    {
        return CategoryInterface::FIELDS_BY_STORE;
    }

    protected function getRepository(): CategoryRepositoryInterface
    {
        return $this->getCategoryRepository();
    }
}
