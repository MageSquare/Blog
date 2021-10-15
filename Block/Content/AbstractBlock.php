<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content;

use MageSquare\Blog\Block\Content\Post\Details;
use Magento\Framework\View\Element\Template;

class AbstractBlock extends Template
{
    /**
     * @var \MageSquare\Blog\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \MageSquare\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \MageSquare\Blog\Helper\Settings
     */
    private $settingsHelper;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    private $context;

    /**
     * @var string
     */
    private $title = '';

    /**
     * @var array
     */
    private $data;

    /**
     * @var \MageSquare\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var array
     */
    private $crumbs = [];

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Url $urlHelper,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        \MageSquare\Blog\Helper\Date $helperDate,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->urlHelper = $urlHelper;
        $this->settingsHelper = $settingsHelper;
        $this->context = $context;
        $this->data = $data;
        $this->helperDate = $helperDate;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $this->addCrumb(
                $breadcrumbs,
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Home'),
                    'link'  => $this->getBaseUrl()
                ]
            );
            $this->addCrumb(
                $breadcrumbs,
                'blog',
                [
                    'label' => $this->getSettingHelper()->getBreadcrumb(),
                    'title' => $this->getSettingHelper()->getBreadcrumb(),
                    'link'  => $this->getUrlResolverModel()->getBlogUrl(),
                ]
            );
        }

        return $this;
    }

    /**
     * @param \Magento\Theme\Block\Html\Breadcrumbs $block
     * @param $key
     * @param $data
     */
    protected function addCrumb(\Magento\Theme\Block\Html\Breadcrumbs $block, $key, $data)
    {
        $this->crumbs[$key] = $data;
        $block->addCrumb($key, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->prepareBreadcrumbs();

        return $this;
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getAmpHeaderHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'MageSquare_Blog::amp/post/header.phtml',
            $post
        );
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getAuthorHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'MageSquare_Blog::post/author.phtml',
            $post
        );
    }

    /**
     * @param null $post
     * @return bool|string
     */
    public function getShortCommentsHtml($post = null)
    {
        return $this->getHtml(
            Details::class,
            'MageSquare_Blog::post/short_comments.phtml',
            $post
        );
    }

    /**
     * @param $post
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesHtml($post = null, $isAmp = false)
    {
        $html = '';
        if ($this->settingsHelper->getUseCategories()) {
            $template = $isAmp ? 'MageSquare_Blog::amp/list/categories.phtml' : 'MageSquare_Blog::list/categories.phtml';

            $html = $this->getHtml(Details::class, $template, $post);
        }

        return $html;
    }

    /**
     * @param $post
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTagsHtml($post = null, $isAmp = false)
    {
        $html = '';
        if ($this->settingsHelper->getUseTags()) {
            $template = $isAmp ? 'MageSquare_Blog::amp/list/tags.phtml' : 'MageSquare_Blog::list/tags.phtml';
            $html = $this->getHtml(Details::class, $template, $post);
        }

        return $html;
    }

    /**
     * @param $blockClass
     * @param $template
     * @param $post
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getHtml($blockClass, $template, $post)
    {
        $block = $this->getLayout()->createBlock($blockClass);
        $html = '';
        if ($block) {
            $block->setPost($post)->setTemplate($template);
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return \Magento\Framework\View\Element\Template\Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return \MageSquare\Blog\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @return \MageSquare\Blog\Model\UrlResolver
     */
    public function getUrlResolverModel()
    {
        return $this->urlResolver;
    }

    /**
     * @return \MageSquare\Blog\Helper\Settings
     */
    public function getSettingHelper()
    {
        return $this->settingsHelper;
    }

    /**
     * @param $datetime
     * @return \Magento\Framework\Phrase|string
     */
    public function renderDate($datetime)
    {
        return $this->helperDate->renderDate($datetime);
    }

    /**
     * @return array
     */
    public function getCrumbs()
    {
        return $this->crumbs;
    }
}
