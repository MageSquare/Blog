<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml\System\Config\Field\Layout;

/**
 * Class Mobile
 */
class Mobile extends \MageSquare\Blog\Block\Adminhtml\System\Config\Field\Layout
{
    /**
     * @return array
     */
    protected function getLayouts()
    {
        $config = [
            ['value' => 'two-columns-left', 'label' => __("Two Columns and Left Sidebar")],
            ['value' => 'two-columns-right', 'label' => __("Two Columns and Right Sidebar")],
        ];

        return $config;
    }
}
