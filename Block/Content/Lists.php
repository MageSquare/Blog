<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\Lists as ListsModel;
use MageSquare\Blog\Model\Posts;
use Magento\Framework\DataObject\IdentityInterface;

class Lists extends AbstractBlock implements IdentityInterface
{
    const PAGER_BLOCK_NAME = 'msblog_list_pager';

    /**
     * @var $collection
     */
    protected $collection;

    /**
     * @var bool
     */
    protected $isCategory = false;

    /**
     * @var bool
     */
    protected $isTag = false;

    /**
     * @var null
     */
    private $toolbar = null;

    /**
     * @var \MageSquare\Blog\Model\ListsFactory
     */
    private $listsModel;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \MageSquare\Blog\Api\TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \MageSquare\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Url $urlHelper,
        \MageSquare\Blog\Api\TagRepositoryInterface $tagRepository,
        \MageSquare\Blog\Api\AuthorRepositoryInterface $authorRepository,
        \MageSquare\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        \MageSquare\Blog\Model\ListsFactory $listsModel,
        \MageSquare\Blog\Helper\Date $helperDate,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $settingsHelper, $urlHelper, $urlResolver, $helperDate, $data);
        $this->listsModel = $listsModel;
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->authorRepository = $authorRepository;
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->getToolbar()
            ->setPagerObject($this->listsModel->create())
            ->setLimit($this->getSettingHelper()->getPostsLimit())
            ->setCollection($this->getCollection());

        return $this;
    }

    /**
     * @param $post
     * @return string
     */
    public function getReadMoreUrl($post)
    {
        return $this->getUrlResolverModel()->getPostUrlById($post->getId());
    }

    /**
     * @param \MageSquare\Blog\Model\ResourceModel\Posts\Collection $collection
     * @return $this
     */
    private function checkTag($collection)
    {
        if (($id = $this->getRequest()->getParam('id')) && $this->isTag) {
            $collection->addTagFilter($id);
        }

        return $this;
    }

    /**
     * @param \MageSquare\Blog\Model\ResourceModel\Posts\Collection $collection
     * @return $this
     */
    private function checkCategory($collection)
    {
        if (($id = $this->getRequest()->getParam('id')) && $this->isCategory) {
            $collection->addCategoryFilter($id);
        }

        return $this;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $posts = $this->postRepository->getActivePosts();

            $posts->setUrlKeyIsNotNull();
            $posts->setDateOrder();

            $this->checkCategory($posts);
            $this->checkTag($posts);

            $this->collection = $posts;
        }

        return $this->collection;
    }

    /**
     * @return \Magento\Framework\View\Element\BlockInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbar()
    {
        if (!$this->toolbar) {
            $toolbar = $this->getLayout()->createBlock(\MageSquare\Blog\Block\Content\Lists\Pager::class);
            $this->getLayout()->setBlock(self::PAGER_BLOCK_NAME, $toolbar);
            $this->toolbar = $toolbar;
        }

        return $this->toolbar;
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getToolbarHtml($isAmp = false)
    {
        $template = $isAmp ? 'MageSquare_Blog::amp/list/pager.phtml' : 'MageSquare_Blog::list/pager.phtml';

        return $this->getToolbar()->setTemplate($template)->toHtml();
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingHelper()->getIconColorClass();
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $cacheTags = [ListsModel::CACHE_TAG];
        $posts = $this->getCollection()->getItems();

        return array_reduce($posts, function (array $carry, Posts $post): array {
            return array_merge($carry, $post->getIdentities());
        }, $cacheTags);
    }

    /**
     * @return \MageSquare\Blog\Api\TagRepositoryInterface
     */
    public function getTagRepository()
    {
        return $this->tagRepository;
    }

    /**
     * @return AuthorRepositoryInterface
     */
    public function getAuthorRepository()
    {
        return $this->authorRepository;
    }

    /**
     * @return \MageSquare\Blog\Api\CategoryRepositoryInterface
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return \MageSquare\Blog\Api\PostRepositoryInterface|\MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getPostRepository()
    {
        return $this->postRepository;
    }

    /**
     * @return Registry
     */
    public function getRegistry(): Registry
    {
        return $this->registry;
    }

    public function isCanRender(): bool
    {
        $collection = $this->getCollection();

        return null !== $collection && $collection->getSize() > 0;
    }
}
