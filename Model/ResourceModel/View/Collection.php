<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\ResourceModel\View;

/**
 * Class
 */
class Collection extends \MageSquare\Blog\Model\ResourceModel\Abstracts\Collection
{
    public function _construct()
    {
        parent::_construct();
        $this->_init(\MageSquare\Blog\Model\View::class, \MageSquare\Blog\Model\ResourceModel\View::class);
    }
}
