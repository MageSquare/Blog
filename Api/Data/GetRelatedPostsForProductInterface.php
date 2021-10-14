<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api\Data;

interface GetRelatedPostsForProductInterface
{
    /**
     * @param int $productId
     * @return PostInterface[]
     */
    public function execute(int $productId): array;
}
