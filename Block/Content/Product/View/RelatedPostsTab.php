<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content\Product\View;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * @method getViewModel()
 * @method setTitle(string $title)
 */
class RelatedPostsTab extends Template implements IdentityInterface
{
    protected function _toHtml()
    {
        /** @var \MageSquare\Blog\ViewModel\Product\View\RelatedPosts $viewModel **/
        $viewModel = $this->getViewModel();
        $this->setTitle($this->_escaper->escapeHtml($viewModel->getBlockTitle()));

        return $viewModel->isCanRender() ? parent::_toHtml() : '';
    }

    public function getIdentities()
    {
        /** @var \MageSquare\Blog\ViewModel\Product\View\RelatedPosts $viewModel * */
        $viewModel = $this->getViewModel();
        $posts = $viewModel->getPostsForCurrentProduct();

        return array_reduce($posts, function (array $carry, IdentityInterface $post): array {
            return array_merge($carry, $post->getIdentities());
        }, []);
    }
}
