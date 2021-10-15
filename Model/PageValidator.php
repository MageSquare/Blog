<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Api\PostRepositoryInterface;
use MageSquare\Blog\Api\TagRepositoryInterface;
use MageSquare\Blog\Helper\Settings;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Block\Html\Pager;

class PageValidator
{
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Pager
     */
    private $pager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    public function __construct(
        PostRepositoryInterface $postRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        AuthorRepositoryInterface $authorRepository,
        Settings $settings,
        Pager $pager,
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        UrlResolver $urlResolver
    ) {
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->authorRepository = $authorRepository;
        $this->settings = $settings;
        $this->pager = $pager;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param $identifier
     * @return int|bool
     */
    public function getPostId($identifier)
    {
        $clean = $this->cleanUrl($identifier);
        $post = $this->postRepository->getByUrlKey($clean);

        return $post->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getCategoryId($identifier, $page = 1)
    {
        $categoryUrlKey = $this->getUrlKey($identifier, $page, CategoryInterface::ROUTE_CATEGORY);
        $category = $this->categoryRepository->getByUrlKeyAndStoreId(
            $categoryUrlKey,
            (int) $this->storeManager->getStore()->getId()
        );

        return $category->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getTagId($identifier, $page = 1)
    {
        $tagUrlKey = $this->getUrlKey($identifier, $page, TagInterface::ROUTE_TAG);
        $tag = $this->tagRepository->getByUrlKeyAndStoreId(
            $tagUrlKey,
            (int) $this->storeManager->getStore()->getId()
        );

        return $tag->getId() ?: false;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getAuthorId($identifier, $page = 1)
    {
        $authorUrlKey = $this->getUrlKey($identifier, $page, AuthorInterface::ROUTE_AUTHOR);
        $author = $this->authorRepository->getByUrlKeyAndStoreId(
            $authorUrlKey,
            (int) $this->storeManager->getStore()->getId()
        );

        return $author->getAuthorId();
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function isIndexRequest($identifier, $page = 1)
    {
        $identifier = str_replace(
            [$this->getOldUrlPostfix($page), $this->getUrlPostfix(), '/', '.html', '.htm'],
            '',
            stristr($identifier, '?', true) ?: $identifier
        );

        return $identifier == $this->settings->getSeoRoute()
            || stristr($identifier, '/', true) == $this->settings->getSeoRoute(); // for back compatibility
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     */
    public function getIsSearchRequest($identifier, $page = 1)
    {
        $clearUrl = $this->cleanUrl($identifier, $page);

        return $clearUrl === \MageSquare\Blog\Helper\Url::ROUTE_SEARCH
            || stristr($clearUrl, '/', true) == \MageSquare\Blog\Helper\Url::ROUTE_SEARCH; // for back compatibility
    }

    /**
     * @param $identifier
     * @param $page
     * @param $route
     * @return bool
     */
    private function getUrlKey($identifier, $page, $route)
    {
        $clean = $this->cleanUrl($identifier, $page);

        if (strpos($clean, "/") === false) {
            return false;
        }

        $parts = explode("/", $clean);

        if ($parts[0] !== $route) {
            return false;
        }

        $urlKey = $parts[1];

        return $urlKey;
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool|string
     */
    private function cleanUrl($identifier, $page = 1)
    {
        $identifier = stristr($identifier, '?', true) ?: $identifier;
        $clean = substr($identifier, strlen($this->settings->getSeoRoute()), strlen($identifier));
        $clean = trim($clean, '/');
        $clean = preg_replace($this->getCleanPattern($page), '', $clean);
        $clean = urldecode($clean);

        return $clean;
    }

    /**
     * @param int $page
     * @return string
     */
    private function getUrlPostfix()
    {
        return $this->settings->getBlogPostfix();
    }

    /**
     * @param int $page
     * @return string
     */
    private function getOldUrlPostfix($page = 1)
    {
        $postfix = $this->settings->getBlogPostfix();

        return $page > 1 ? "/{$page}{$postfix}" : $postfix;
    }

    /**
     * @param $page
     * @return string
     */
    private function getCleanPattern($page)
    {
        return '/(' . preg_quote($this->getUrlPostfix(), '/')
            . '|' . preg_quote($this->getOldUrlPostfix($page), '/') . ')$/';
    }

    /**
     * @param $identifier
     * @param $id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightPostSyntax($identifier, $id)
    {
        $url = $this->urlResolver->getPostUrlById($id);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightCategorySyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getCategoryUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightTagSyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getTagUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $id
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightAuthorSyntax($identifier, $id, $page = 1)
    {
        $url = $this->urlResolver->getAuthorUrlById($id, $page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param int $page
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isRightBlogSyntax($identifier, $page = 1)
    {
        $url = $this->urlResolver->getBlogUrl($page);

        return $this->isRightSyntax($identifier, $url);
    }

    /**
     * @param $identifier
     * @param $url
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function isRightSyntax($identifier, $url)
    {
        if (!$this->settings->getRedirectToSeoFormattedUrl()) {
            return true;
        }

        $stdPage = (bool)$this->request->getParam($this->pager->getPageVarName());
        $required = str_replace($this->storeManager->getStore()->getBaseUrl(), '', $url);

        return (strtolower($identifier) == strtolower($required)) && !$stdPage;
    }

    public function isNeedRedirectToPageWithPostfix(string $requestString): bool
    {
        $result = false;

        if ($this->settings->getRedirectToSeoFormattedUrl()) {
            $escapedPostfix = str_replace('.', '\.', $this->getUrlPostfix());

            if (strlen($escapedPostfix) > 1) {
                $result = !preg_match("/.+{$escapedPostfix}($|\/|\?.*)/mD", $requestString);
            }
        }

        return $result;
    }
}
