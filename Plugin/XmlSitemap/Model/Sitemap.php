<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Plugin\XmlSitemap\Model;

/**
 * Class Sitemap
 */
class Sitemap
{
    /**
     * @var \MageSquare\Blog\Model\SitemapFactory
     */
    private $sitemapFactory;

    public function __construct(
        \MageSquare\Blog\Model\SitemapFactory $sitemapFactory
    ) {
        $this->sitemapFactory = $sitemapFactory;
    }

    /**
     * @param \MageSquare\XmlSitemap\Model\Sitemap $subgect
     * @param \Closure $proceed
     * @param $storeId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetBlogProLinks(\Magento\Framework\Model\AbstractModel $subgect, \Closure $proceed, $storeId)
    {
        /** @var \MageSquare\Blog\Model\Sitemap $blogSitemap */
        $blogSitemap = $this->sitemapFactory->create();
        $blogLinks = $blogSitemap->generateLinks($storeId);

        return $blogLinks;
    }
}
