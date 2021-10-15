<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Comments;

/**
 * Class Reply
 */
class Reply extends \MageSquare\Blog\Controller\Adminhtml\Comments
{
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_redirect('*/*/edit', ['reply_to_id' => $this->getRequest()->getParam('id')]);
    }
}
