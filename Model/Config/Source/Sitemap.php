<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class
 */
class Sitemap implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => \MageSquare\Blog\Model\Sitemap::AMBLOG_TYPE_BLOG, 'label' => __('Blog')],
            ['value' => \MageSquare\Blog\Model\Sitemap::AMBLOG_TYPE_POST, 'label' => __('Posts')],
            ['value' => \MageSquare\Blog\Model\Sitemap::AMBLOG_TYPE_CATEGORY, 'label' => __('Categories')],
            ['value' => \MageSquare\Blog\Model\Sitemap::AMBLOG_TYPE_TAG, 'label' => __('Tags')],
        ];
    }
}
