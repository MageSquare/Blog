<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Postfix implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('No Postfix')],
            ['value' => '.html', 'label' => __('.html')],
            ['value' => '/', 'label' => '/']
        ];
    }
}
