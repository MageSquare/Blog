<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content\Post;

use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Api\CommentRepositoryInterface;
use MageSquare\Blog\Api\TagRepositoryInterface;
use MageSquare\Blog\Helper\Date;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\ConfigProvider;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Details extends Template
{
    /**
     * @var Date
     */
    private $helperDate;

    /**
     * @var CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \MageSquare\Blog\Model\Posts
     */
    private $post;

    /**
     * @var ObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        Date $helperDate,
        TagRepositoryInterface $tagRepository,
        CategoryRepositoryInterface $categoryRepository,
        CommentRepositoryInterface $commentRepository,
        ConfigProvider $configProvider,
        ObjectFactory $dataObjectFactory,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperDate = $helperDate;
        $this->commentRepository = $commentRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->configProvider = $configProvider;
        $this->registry = $registry;
    }

    /**
     * @param $post
     *
     * @return $this
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return \MageSquare\Blog\Model\Posts
     */
    public function getPost()
    {
        if ($this->post === null) {
            $this->post = $this->registry->registry(Registry::CURRENT_POST);
        }

        return $this->post;
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
     * @return string
     */
    public function getCommentsUrl()
    {
        return $this->getPost()->getUrl() . "#comments";
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCommentsCount()
    {
        $commentsCollection = $this->commentRepository->getCommentsInPost($this->getPost()->getId())->addActiveFilter();

        return $commentsCollection->getSize();
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTagsHtml($isAmp = false)
    {
        $template = $isAmp ? 'MageSquare_Blog::amp/list/tags.phtml' : 'MageSquare_Blog::list/tags.phtml';

        return $this->getHtml(\MageSquare\Blog\Block\Content\Post\Details::class, $template);
    }

    /**
     * @param bool $isAmp
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCategoriesHtml($isAmp = false)
    {
        $template = $isAmp ? 'MageSquare_Blog::amp/list/categories.phtml' : 'MageSquare_Blog::list/categories.phtml';

        return $this->getHtml(\MageSquare\Blog\Block\Content\Post\Details::class, $template);
    }

    /**
     * @param $blockClass
     * @param $template
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getHtml($blockClass, $template)
    {
        $block = $this->getLayout()->createBlock($blockClass);
        $html = '';
        if ($block) {
            $block->setPost($this->getPost())->setTemplate($template);
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return array|\Magento\Framework\DataObject[]
     */
    public function getTags()
    {
        if ($this->getPost()->isPreviewPost()) {
            $result = [];
            $tags = $this->getPost()->getData('tags');
            $tagsArray = explode(',', $tags);
            foreach ($tagsArray as $tag) {
                if ($tag) {
                    $result[] = $this->dataObjectFactory->create(
                        [
                            'data' => [
                                'name' => $tag
                            ]
                        ]
                    );
                }
            }

            return $result;
        }

        return $this->tagRepository->getTagsByIds($this->getPost()->getTagIds())->getItems();
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategories()
    {
        $categories = $this->getPost()->getCategories();

        if (!is_array($categories)) {
            $categories = $categories ? explode(',', $categories) : [];
        }

        $collection = $this->categoryRepository->getCategoriesByIds($categories);
        $limit = $this->configProvider->getCategoryLimitOnPost();
        if ($limit) {
            $collection->setPageSize($limit);
        }

        return $collection;
    }

    public function isShowAuthorInfo(): bool
    {
        return $this->configProvider->isShowAuthorInfo();
    }

    public function getColorClass(): string
    {
        return $this->configProvider->getIconColorClass();
    }
}
