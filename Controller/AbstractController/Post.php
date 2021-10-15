<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\AbstractController;

use MageSquare\Blog\Model\Blog\MetaDataResolver\Post as MetaResolver;
use MageSquare\Blog\Model\Blog\Registry;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Post extends Action
{
    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \MageSquare\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Magento\Store\App\Response\Redirect
     */
    private $redirect;

    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    private $session;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MetaResolver
     */
    private $metaDataResolver;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Registry $registry,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        \Magento\Store\App\Response\Redirect $redirect,
        \MageSquare\Blog\Helper\Url $urlHelper,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        \Magento\Catalog\Model\Session $session,
        PageFactory $resultPageFactory,
        MetaResolver $metaDataResolver,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->urlHelper = $urlHelper;
        $this->redirect = $redirect;
        $this->urlResolver = $urlResolver;
        $this->session = $session;
        $this->resultPageFactory = $resultPageFactory;
        $this->metaDataResolver = $metaDataResolver;
        $this->logger = $logger;
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        if (strpos($this->getRequest()->getPathInfo(), '/amp/') !== false) {
            $this->urlHelper->addAmpHeaders($this->getResponse());
        }

        $postId = (int)$this->getRequest()->getParam('id');
        if ($postId) {
            try {
                $post = $this->postRepository->getById($postId);
                $this->registry->register(Registry::CURRENT_POST, $post, true);
                $this->session->setCurrentPostId($post->getId());

                $this->metaDataResolver->resolve($page, $post);
                return $page;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                $this->messageManager->addErrorMessage(
                    __('Something went wrong. Please review the error log.')
                );
            }
        }

        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $redirect->setPath($this->urlResolver->getBlogUrl());
    }
}
