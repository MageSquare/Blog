<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Ui\DataProvider\Posts\RelatedProducts;

use MageSquare\Blog\Api\Data\GetPostRelatedProductsInterface;
use MageSquare\Blog\Model\ResourceModel\Posts\RelatedProducts\GetPostRelatedProducts;
use Magento\Catalog\Helper\Image;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class DataModifier implements ModifierInterface
{
    /**
     * @var GetPostRelatedProductsInterface
     */
    private $getPostRelatedProducts;

    /**
     * @var Image
     */
    private $imageProvider;

    public function __construct(
        GetPostRelatedProductsInterface $getPostRelatedProducts,
        Image $imageProvider
    ) {
        $this->getPostRelatedProducts = $getPostRelatedProducts;
        $this->imageProvider = $imageProvider;
    }

    public function modifyData(array $data)
    {
        if (isset($data['post_id'])) {
            $relatedPostData = $this->getRelatedProductsData((int)$data['post_id']);
            $data['related_products_ids']['related_products_container'] = $relatedPostData;
        }

        return $data;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    private function getRelatedProductsData(int $postId): array
    {
        $productsData = [];

        foreach ($this->getPostRelatedProducts->execute($postId) as $product) {
            $productsData[] = [
                'entity_id' => $product->getId(),
                GetPostRelatedProducts::POSITION_ALIAS => $product->getData(GetPostRelatedProducts::POSITION_ALIAS),
                'websites' => join(',', (array)$product->getWebsiteIds()),
                'name' => $product->getName(),
                'visibility' => $product->getVisibility(),
                'status' => $product->getStatus(),
                'thumbnail' => $this->imageProvider->init($product, 'product_listing_thumbnail')->getUrl()
            ];
        }

        return $productsData;
    }
}
