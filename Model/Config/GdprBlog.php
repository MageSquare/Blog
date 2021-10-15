<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;

class GdprBlog implements OptionSourceInterface
{
    const GDPR_BLOG_COMMENT_FORM = 'msblog_comment_form';

    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => [
                    ['value' => self::GDPR_BLOG_COMMENT_FORM, 'label' => __('Comment Form')]
                ],
                'label' => __('Blog Pro')
            ]
        ];
    }
}
