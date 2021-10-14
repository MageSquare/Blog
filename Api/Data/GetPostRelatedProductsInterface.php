<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Api\Data;

use Magento\Catalog\Api\Data\ProductInterface;

/**
 * @api
 */
interface GetPostRelatedProductsInterface
{
    /**
     * @param int $postId
     * @return ProductInterface[]
     */
    public function execute(int $postId): array;
}
