<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Import;

use MageSquare\Blog\Api\PostRepositoryInterface;
use MageSquare\Blog\Api\TagRepositoryInterface;
use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\CommentRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use MageSquare\Base\Model\Serializer;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AbstractImport
 */
class AbstractImport extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var PostRepositoryInterface
     */
    protected $postRepository;

    /**
     * @var TagRepositoryInterface
     */
    protected $tagRepository;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var AuthorRepositoryInterface
     */
    protected $authorRepository;

    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \MageSquare\Blog\Helper\Url
     */
    protected $urlHelper;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        PostRepositoryInterface $postRepository,
        TagRepositoryInterface $tagRepository,
        CategoryRepositoryInterface $categoryRepository,
        AuthorRepositoryInterface $authorRepository,
        CommentRepositoryInterface $commentRepository,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \MageSquare\Blog\Helper\Url $urlHelper,
        DateTime $dateTime,
        Serializer $serializer,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->postRepository = $postRepository;
        $this->tagRepository = $tagRepository;
        $this->categoryRepository = $categoryRepository;
        $this->authorRepository = $authorRepository;
        $this->commentRepository = $commentRepository;
        $this->mediaConfig = $mediaConfig;
        $this->urlHelper = $urlHelper;
        $this->dateTime = $dateTime;
        $this->serializer = $serializer;
        $this->storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->scopeConfig = $scopeConfig;
        $this->logger = $context->getLogger();
    }
}
