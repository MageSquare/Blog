<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml\Categories\Edit;

use MageSquare\Blog\Controller\Adminhtml\Categories\Edit;
use MageSquare\Blog\Model\Categories;

class DeleteButton extends \MageSquare\Blog\Block\Adminhtml\DeleteButton
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->getCurrentCategory()->getCategoryId();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConfirmText()
    {
        if ($this->getCurrentCategory()->hasChildren()) {
            return __('This category has children categories. Are you sure you want to delete this?');
        }

        return parent::getConfirmText();
    }

    /**
     * @return Categories
     */
    public function getCurrentCategory()
    {
        return $this->getRegistry()->registry(Edit::CURRENT_MAGESQUARE_BLOG_CATEGORY);
    }
}
