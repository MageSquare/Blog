<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\ResourceModel\Posts\RelatedProducts;

use MageSquare\Blog\Api\Data\GetPostRelatedProductsInterface;
use MageSquare\Blog\Model\Posts\RelatedProducts\Products\CollectionModifierInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\DB\Select;

class GetPostRelatedProducts implements GetPostRelatedProductsInterface
{
    const POST_PRODUCT_RELATION_TABLE = 'magesquare_blog_posts_products';
    const POSITION_ALIAS = 'magesquare_blog_position';
    const POST_ID = 'post_id';
    const PRODUCT_ID = 'product_id';
    const POSITION = 'position';

    /**
     * @var ProductCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionModifierInterface[]
     */
    private $collectionModifiers = [];

    /**
     * @var ProductCollection[]
     */
    private $collectionCache = [];

    public function __construct(
        ProductCollectionFactory $collectionFactory,
        array $collectionModifiers = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionModifiers = $collectionModifiers;
    }

    /**
     * @param int $postId
     * @return ProductInterface[]
     */
    public function execute(int $postId): array
    {
        if (!isset($this->collectionCache[$postId])) {
            $collection = $this->collectionFactory->create();
            $productSelect = $collection->getSelect();
            $productSelect->joinInner(
                ['abpp' => $collection->getTable(self::POST_PRODUCT_RELATION_TABLE)],
                "e.entity_id = abpp.product_id and abpp.post_id = {$postId}",
                [self::POSITION_ALIAS => self::POSITION]
            );
            $productSelect->order(sprintf(
                '%s %s',
                self::POSITION_ALIAS,
                Select::SQL_ASC
            ));

            foreach ($this->collectionModifiers as $modifier) {
                if ($modifier instanceof CollectionModifierInterface) {
                    $modifier->modify($collection);
                }
            }

            $this->collectionCache[$postId] = $collection;
        }

        return $this->collectionCache[$postId]->getItems();
    }
}
