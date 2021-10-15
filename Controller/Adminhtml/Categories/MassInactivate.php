<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Categories;

use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Model\Source\CategoryStatus;

/**
 * Class
 */
class MassInactivate extends AbstractMassAction
{
    /**
     * @param CategoryInterface $category
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($category)
    {
        try {
            $category->setStatus(CategoryStatus::STATUS_DISABLED);
            $this->getRepository()->save($category);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
