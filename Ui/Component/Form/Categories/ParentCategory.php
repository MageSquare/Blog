<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Ui\Component\Form\Categories;

use MageSquare\Blog\Api\Data\CategoryInterface;
use Magento\Framework\Data\OptionSourceInterface;
use MageSquare\Blog\Model\ResourceModel\Categories\CollectionFactory as CategoriesCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\Category as CategoryModel;

/**
 * Class
 */
class ParentCategory extends \MageSquare\Blog\Ui\Component\Form\Categories implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCategoriesTree(true);
    }
}
