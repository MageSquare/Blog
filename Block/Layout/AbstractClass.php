<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Layout;

/**
 * Class
 */
class AbstractClass extends \Magento\Framework\View\Element\Template
{
    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHead()
    {
        return $this->getLayout()->getBlock('head');
    }

    /**
     * @return bool|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getExtraHead()
    {
        return $this->getLayout()->getBlock('extra_head');
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function wantAsserts()
    {
        $result = true;
        $license = $this->getLayout()->getBlock('layout');
        if ($license) {
            $alias = $this->getNameInLayout();
            $parts = explode(".", $alias);
            $alias = $parts[count($parts) - 1];
            $result = $license->isBlockUsed($alias);
        }

        return $result;
    }
}
