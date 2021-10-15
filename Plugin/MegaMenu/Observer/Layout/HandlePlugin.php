<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Plugin\MegaMenu\Observer\Layout;

use MageSquare\Blog\Helper\Data;

class HandlePlugin
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
     * @param \MageSquare\MegaMenu\Observer\Layout\Handle $subject
     * @param \Closure $proceed
     * @param $observer
     */
    public function aroundExecute($subject, \Closure $proceed, $observer)
    {
        if (!$this->helper->isCurrentPageAmp()) {
            $proceed($observer);
        }
    }
}
