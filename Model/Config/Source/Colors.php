<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Colors
 */
class Colors implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '-classic', 'label' => __('Classic')],
            ['value' => '-red', 'label' => __('Red')],
            ['value' => '-green', 'label' => __('Green')],
            ['value' => '-blue', 'label' => __('Blue')],
            ['value' => '-grey', 'label' => __('Grey')],
            ['value' => '-purple', 'label' => __('Purple')],
        ];
    }
}
