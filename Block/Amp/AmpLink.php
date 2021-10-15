<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Amp;

use Magento\Framework\View\Element\Template;

class AmpLink extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \MageSquare\Blog\Helper\Data
     */
    private $dataHelper;

    public function __construct(
        \MageSquare\Blog\Helper\Data $dataHelper,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return string
     */
    public function getCanonicalUrl()
    {
        $query = $this->getRequest()->getQuery()->toArray();
        unset($query['amp']);

        if ($query) {
            $result = [
                '_current' => true,
                '_use_rewrite' => true,
                '_query' => $query
            ];
        } else {
            $result = ['_use_rewrite' => true];
        }

        return $this->getUrl('*/*/*', $result);
    }

    /**
     * @return string
     */
    public function getAmpLink()
    {
        $ampLink = null;

        if ($this->dataHelper->isAmpEnable()) {
            $ampLink = $this->getBaseUrl() . 'amp' . $this->getRequest()->getPathInfo();
        }

        return $ampLink;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->pageConfig->getDescription();
    }
}
