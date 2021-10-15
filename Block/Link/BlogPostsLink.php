<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Link;

use Magento\Framework\View\Element\Html\Link\Current;

class BlogPostsLink extends Current implements \Magento\Customer\Block\Account\SortLinkInterface
{

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->isAllowed() ? parent::_toHtml() : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * @return bool
     */
    protected function isAllowed()
    {
        return true;
    }
}
