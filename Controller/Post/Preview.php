<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Post;

use MageSquare\Base\Model\Serializer;
use MageSquare\Blog\Helper\Url;
use MageSquare\Blog\Model\Blog\MetaDataResolver\Post as MetadataResolver;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\PostsFactory;
use MageSquare\Blog\Model\Preview\PreviewSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\App\Response\Redirect;

class Preview extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PostsFactory
     */
    private $postsFactory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * @var PreviewSession
     */
    private $previewSession;

    /**
     * @var Block
     */
    private $cache;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MetadataResolver
     */
    private $metadataResolver;

    public function __construct(
        Context $context,
        Registry $registry,
        PostsFactory $postsFactory,
        Redirect $redirect,
        Url $urlHelper,
        PreviewSession $previewSession,
        Block $cache,
        Serializer $serializer,
        MetadataResolver $metadataResolver,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->postsFactory = $postsFactory;
        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        $this->redirect = $redirect;
        $this->previewSession = $previewSession;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->resultPageFactory = $resultPageFactory;
        $this->metadataResolver = $metadataResolver;
    }

    /**
     * @return ResponseInterface|ResultInterface|\Magento\Framework\View\Result\Page
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $data = $this->getSavedData();

        if ($data) {
            if (strpos($this->getRequest()->getPathInfo(), '/amp/') !== false) {
                $this->urlHelper->addAmpHeaders($this->getResponse());
            }

            $page = $this->resultPageFactory->create();
            $post = $this->postsFactory->create();
            $post->addData($data);
            $post->setIsPreviewPost(true);
            $post->setCommentsEnabled(false);
            $this->registry->unregister(Registry::CURRENT_POST);
            $this->registry->register(Registry::CURRENT_POST, $post);
            $this->metadataResolver->resolve($page, $post);

            return $page;
        } else {
            return $this->_redirect('noroute');
        }
    }

    /**
     * @return array
     */
    protected function getSavedData()
    {
        $data = [];
        $blogKey = $this->getRequest()->getParam('msblog_key');
        if ($blogKey) {
            $data = $this->cache->load($blogKey);
            if ($data) {
                $this->cache->remove($blogKey);
                $data = $this->serializer->unserialize($data);
                $this->previewSession->setPostData($data);
            }
        }

        if (!$data) {
            $data = $this->previewSession->getPostData();
        }

        return $data ?: [];
    }
}
