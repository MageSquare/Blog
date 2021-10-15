<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content\Post;

use MageSquare\Blog\Model\Posts;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * @method getViewModel()
 */
class RelatedProducts extends Template implements IdentityInterface
{
    public function getIdentities()
    {
        /** @var \MageSquare\Blog\ViewModel\Posts\RelatedProducts $viewModel **/
        $viewModel = $this->getViewModel();
        $currentPost = $viewModel->getCurrentPost();
        $identities = [Posts::CACHE_TAG . '_' . $currentPost->getPostId()];

        return array_reduce($viewModel->getPostProducts(), function (array $carry, Product $product): array {
            return array_merge($carry, $product->getIdentities());
        }, $identities);
    }
}
