<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Ui\Component\Form\Post;

class Authors extends \MageSquare\Blog\Ui\Component\Listing\Post\Authors
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        if (count($options) >= 1) {
            array_unshift($options, ['label' => __('Select...')->render(), 'value' => 0]);
        }
        return $options;
    }
}
