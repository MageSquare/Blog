<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Index;

/**
 * Class Redirect
 */
class Redirect extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        if ($url = $this->getRequest()->getParam('url')) {
            $this->getResponse()->setRedirect($url, 301)->sendHeaders();
        }
    }
}
