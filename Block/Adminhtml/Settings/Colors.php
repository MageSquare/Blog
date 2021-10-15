<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml\Settings;

/**
 * Class
 */
class Colors extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return bool|string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $render = $this->getLayout()->createBlock(
            \MageSquare\Blog\Block\Adminhtml\System\Config\Form\Element\Colors\Render::class
        );

        return $render ? $render->toHtml() : false;
    }
}
