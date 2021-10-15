<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Plugin\ShopbyBrand\Block;

use MageSquare\Blog\Helper\Data;

class BrandsPopupPlugin
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param \MageSquare\ShopbyBrand\Block\BrandsPopup $subject
     */
    public function beforeToHtml(\MageSquare\ShopbyBrand\Block\BrandsPopup $subject)
    {
        if ($this->helper->isCurrentPageAmp()) {
            $subject->setTemplate('MageSquare_Blog::amp/brands_popup.phtml');
        }
    }
}
