<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Posts\RelatedProducts\Products;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class BackendViewCollectionModifier implements CollectionModifierInterface
{
    public function modify(ProductCollection $collection): void
    {
        $collection->addAttributeToSelect(ProductInterface::VISIBILITY);
        $collection->addAttributeToSelect(ProductInterface::STATUS);
        $collection->addAttributeToSelect('websites');
        $collection->addAttributeToSelect('thumbnail');
    }
}
