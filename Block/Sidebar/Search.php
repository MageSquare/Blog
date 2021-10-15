<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Sidebar;

class Search extends AbstractClass
{
    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Date $dateHelper,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->urlResolver = $urlResolver;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("MageSquare_Blog::sidebar/search.phtml");
        $this->addAmpTemplate("MageSquare_Blog::amp/sidebar/search.phtml");
        $this->setRoute('display_search');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getBlockHeader()
    {
        return __('Search The Blog');
    }

    /**
     * @return string
     */
    public function getSearchUrl()
    {
        return $this->urlResolver->getSearchPageUrl();
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->stripTags($this->getRequest()->getParam('query'));
    }

    /**
     * @return string
     */
    public function getAmpSearchUrl()
    {
        return str_replace(['https:', 'http:'], '', $this->getSearchUrl());
    }
}
