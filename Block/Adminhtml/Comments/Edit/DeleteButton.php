<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml\Comments\Edit;

use MageSquare\Blog\Controller\Adminhtml\Comments\Edit;

class DeleteButton extends \MageSquare\Blog\Block\Adminhtml\DeleteButton
{
    /**
     * @return int
     */
    public function getItemId()
    {
        return (int)$this->getRegistry()->registry(Edit::CURRENT_MAGESQUARE_BLOG_COMMENT)->getCommentId();
    }
}
