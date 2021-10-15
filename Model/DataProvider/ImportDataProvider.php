<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use MageSquare\Blog\Model\ResourceModel\Comments\CollectionFactory;

/**
 * Class ImportDataProvider
 */
class ImportDataProvider extends AbstractDataProvider
{
    /**
     * @return array
     */
    public function getData()
    {
        return [];
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return null
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        return null;
    }
}
