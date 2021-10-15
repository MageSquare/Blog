<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Content\Post;

use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Model\Blog\Registry;
use Magento\Framework\View\Element\Template;

class Related extends Template
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var PostInterface
     */
    private $post;

    /**
     * @var \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    protected $collection = null;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \MageSquare\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    public function __construct(
        Template\Context $context,
        Registry $registry,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        \MageSquare\Blog\Helper\Date $helperDate,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->postRepository = $postRepository;
        $this->helperDate = $helperDate;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getCollection()
    {
        if (!$this->collection && $this->getPost() && $this->getPost()->getRelatedPostIds()) {
            $postIds = explode(',', $this->getPost()->getRelatedPostIds());
            $this->collection = $this->postRepository->getActivePosts()
                ->addFieldToFilter(PostInterface::POST_ID, ['in' => $postIds])
                ->setUrlKeyIsNotNull()
                ->setDateOrder();
        }

        return $this->collection;
    }

    /**
     * @return PostInterface
     */
    public function getPost()
    {
        if ($this->post === null) {
            $this->post = $this->registry->registry(Registry::CURRENT_POST);
        }

        return $this->post;
    }

    /**
     * @param $post
     * @return string
     */
    public function getReadMoreUrl($post)
    {
        return $this->urlResolver->getPostUrlById($post->getId());
    }

    /**
     * @param $datetime
     * @return \Magento\Framework\Phrase|string
     */
    public function renderDate($datetime)
    {
        return $this->helperDate->renderDate($datetime);
    }
}
