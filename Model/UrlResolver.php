<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Api\PostRepositoryInterface;
use MageSquare\Blog\Api\TagRepositoryInterface;
use MageSquare\Blog\Controller\AbstractController\Search;
use MageSquare\Blog\Helper\Settings;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

class UrlResolver
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
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        PostRepositoryInterface $postRepository,
        CategoryRepositoryInterface $categoryRepository,
        TagRepositoryInterface $tagRepository,
        AuthorRepositoryInterface $authorRepository,
        Settings $settings,
        StoreManagerInterface $storeManager,
        ConfigProvider $configProvider,
        RequestInterface $request
    ) {
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->postRepository = $postRepository;
        $this->authorRepository = $authorRepository;
        $this->settings = $settings;
        $this->storeManager = $storeManager;
        $this->configProvider = $configProvider;
        $this->request = $request;
    }

    /**
     * @param $id
     * @return string
     */
    public function getPostUrlById($id)
    {
        try {
            $url = $this->postRepository->getById($id)->getUrl();
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getCategoryUrlById($id, $page = 1)
    {
        try {
            $category = $this->categoryRepository->getByIdAndStore(
                $id,
                (int) $this->storeManager->getStore()->getId()
            );
            $url = $category->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getTagUrlById($id, $page = 1)
    {
        try {
            $tag = $this->tagRepository->getByIdAndStore(
                $id,
                (int) $this->storeManager->getStore()->getId()
            );
            $url = $tag->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    /**
     * @param $id
     * @param $page
     * @return string
     */
    public function getAuthorUrlById($id, $page = 1)
    {
        try {
            $author = $this->authorRepository->getByIdAndStore(
                $id,
                (int) $this->storeManager->getStore()->getId()
            );
            $url = $author->getUrl($page);
        } catch (NoSuchEntityException $e) {
            $url = '';
        }

        return $url;
    }

    public function getSearchPageUrl($page = 1, ?StoreInterface $store = null): string
    {
        $store = $store ?: $this->storeManager->getStore();
        return $this->getBaseUrl($store)
            . $this->configProvider->getSeoRoute($store)
            . '/' . \MageSquare\Blog\Helper\Url::ROUTE_SEARCH
            . $this->getUrlPostfix($page);
    }

    public function getSearchPageUrlWithQuery($page = 1, ?StoreInterface $store = null): string
    {
        $url = $this->getSearchPageUrl($page, $store);
        $delimiter = '';
        if ($this->getQuery()) {
            $delimiter = strpos($url, '?') === false ? '?' : '&';
            $delimiter .= Search::SEARCH_PARAM . '=';
        }

        return $url . $delimiter . $this->getQuery();
    }

    public function getQuery(): string
    {
        return (string) $this->request->getParam(Search::SEARCH_PARAM, '');
    }

    /**
     * @param int $page
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBlogUrl($page = 1, ?StoreInterface $store = null)
    {
        $store = $store ?: $this->storeManager->getStore();
        return $this->getBaseUrl($store)
            . $this->configProvider->getSeoRoute($store)
            . $this->getUrlPostfix($page);
    }

    private function getBaseUrl(StoreInterface $store): string
    {
        return $store->getBaseUrl();
    }

    private function getUrlPostfix($page = 1, ?StoreInterface $store = null): string
    {
        $postfix = $this->configProvider->getBlogPostfix($store);

        return $page > 1 ? "{$postfix}?p={$page}" : $postfix;
    }

    public function getUrlWithPostfix(string $identifierWithoutPostfix, ?StoreInterface $store = null): string
    {
        $urlParts = explode('?', $identifierWithoutPostfix);
        $paramsPart = !empty($urlParts[1]) ? '?' . $urlParts[1] : '';
        $routePath = ltrim($urlParts[0], '/');
        $store = $store ?: $this->storeManager->getStore();
        $baseUrl = rtrim($this->getBaseUrl($store), '/');
        $postfix = $this->configProvider->getBlogPostfix($store);

        return sprintf('%s/%s%s%s', $baseUrl, $routePath, $postfix, $paramsPart);
    }
}
