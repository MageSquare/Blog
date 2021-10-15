<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Posts\RelatedProducts\Products;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Store\Model\StoreManagerInterface;

class AddFrontendDataModifier implements CollectionModifierInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var StockHelper
     */
    private $stockHelper;

    public function __construct(
        StoreManagerInterface $storeManager,
        StockHelper $stockHelper
    ) {
        $this->storeManager = $storeManager;
        $this->stockHelper = $stockHelper;
    }

    public function modify(ProductCollection $collection): void
    {
        $this->stockHelper->addIsInStockFilterToCollection($collection);
        $currentStore = $this->storeManager->getStore();
        $collection->addPriceData();
        $collection->addUrlRewrite($currentStore->getRootCategoryId());
        $collection->setStore($currentStore);
    }
}
