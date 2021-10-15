<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Posts;

use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Model\Source\PostStatus;

/**
 * Class
 */
class MassActivate extends AbstractMassAction
{
    /**
     * @param $post
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function itemAction($post)
    {
        try {
            $post->setStatus(PostStatus::STATUS_ENABLED);
            $this->getRepository()->save($post);
        } catch (\Exception $e) {
            $this->getMessageManager()->addErrorMessage($e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}
