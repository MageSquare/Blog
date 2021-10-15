<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model;

class Sitemap extends \Magento\Framework\Model\AbstractModel
{
    const AMBLOG_TYPE_BLOG = 'blog';

    const AMBLOG_TYPE_POST = 'post';

    const AMBLOG_TYPE_CATEGORY = 'category';

    const AMBLOG_TYPE_TAG = 'tag';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \MageSquare\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \MageSquare\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \MageSquare\Blog\Helper\Settings
     */
    private $settingsHelper;

    protected function _construct()
    {
        parent::_construct();
        $this->dateTime = $this->getData('date_time');
        $this->urlResolver = $this->getData('url_resolver');
        $this->postRepository = $this->getData('post_repository');
        $this->categoryRepository = $this->getData('category_repository');
        $this->tagRepository = $this->getData('tag_repository');
        $this->storeManager = $this->getData('store_manager');
        $this->settingsHelper = $this->getData('settings_helper');
    }

    /**
     * @param null $storeId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateLinks($storeId = null)
    {
        $storeId = $storeId ? : $this->storeManager->getStore()->getId();

        $links = [];
        $currentDate = $this->dateTime->gmtDate('Y-m-d');
        $links[] = ['url' => $this->urlResolver->getBlogUrl(), 'date' => $currentDate];

        $posts = $this->postRepository->getActivePosts($storeId)->setDateOrder();
        $links = array_merge($links, $this->generateEntityLinks($posts, $storeId, $currentDate));

        $categories = $this->categoryRepository->getActiveCategories($storeId)->setSortOrder('asc');
        $links = array_merge($links, $this->generateEntityLinks($categories, $storeId, $currentDate));

        $tags = $this->tagRepository->getActiveTags($storeId)
            ->setMinimalPostCountFilter($this->settingsHelper->getTagsMinimalPostCount())
            ->setNameOrder();
        $links = array_merge($links, $this->generateEntityLinks($tags, $storeId, $currentDate));

        return $links;
    }

    /**
     * @param $entities
     * @param $storeId
     * @param $currentDate
     * @return array
     */
    private function generateEntityLinks($entities, $storeId, $currentDate)
    {
        $links = [];
        foreach ($entities as $entity) {
            $entity->setStoreId($storeId);
            $links[] = ['url' => $entity->getUrl(), 'date' => $currentDate];
        }

        return $links;
    }
}
