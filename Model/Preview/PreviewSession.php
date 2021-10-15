<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Preview;

class PreviewSession extends \Magento\Framework\Session\SessionManager
{
    /**
     * @param array $postData
     * @return $this
     */
    public function setPostData($postData = [])
    {
        $this->storage->setData('post_data', $postData);
        return $this;
    }

    /**
     * @return array
     */
    public function getPostData()
    {
        $postData = $this->storage->getData('post_data');
        return is_array($postData) ? $postData : [];
    }

    /**
     * @return bool
     */
    public function hasPostData()
    {
        return !empty($this->getPostData());
    }

    /**
     * Retrieve session name
     *
     * @return string
     */
    public function getName()
    {
        return 'mslog_preview_session';
    }
}
