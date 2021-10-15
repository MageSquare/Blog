<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Plugin\MegaMenu\Model\Menu;

use MageSquare\Blog\Block\Link;
use MageSquare\Blog\Model\UrlResolver;

class TreeResolverPlugin
{
    /**
     * @var \MageSquare\Blog\Helper\Settings
     */
    private $settings;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var Link
     */
    private $link;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    public function __construct(
        \MageSquare\Blog\Helper\Settings $settings,
        \Magento\Framework\UrlInterface $url,
        Link $link,
        UrlResolver $urlResolver
    ) {
        $this->settings = $settings;
        $this->url = $url;
        $this->link = $link;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @param \MageSquare\MegaMenu\Model\Menu\TreeResolver $subject
     * @param $items
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetAdditionalLinks(
        \MageSquare\MegaMenu\Model\Menu\TreeResolver $subject,
        $items
    ) {
        if (!$this->settings->showInNavMenu()) {
            return $items;
        }

        $url = $this->urlResolver->getBlogUrl();
        $items[] = [
            'name' => $this->link->getLabel(),
            'id' => 'magesquare_blog',
            'url' => $url,
            'has_active' => false,
            'content' => '',
            'width' => 1,
            'is_active' => $url == $this->url->getCurrentUrl()
        ];

        return $items;
    }
}
