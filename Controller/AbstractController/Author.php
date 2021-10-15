<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\AbstractController;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver\Author as MetaResolver;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\UrlResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

class Author extends Action
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MetaResolver
     */
    private $metaDataResolver;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        Registry $registry,
        UrlResolver $urlResolver,
        PageFactory $resultPageFactory,
        MetaResolver $metaDataResolver,
        AuthorRepositoryInterface $authorRepository,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->urlResolver = $urlResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->metaDataResolver = $metaDataResolver;
        $this->authorRepository = $authorRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $page = $this->resultPageFactory->create();
                $author = $this->authorRepository->getById($id);
                $this->registry->register(Registry::CURRENT_AUTHOR, $author, true);

                $this->metaDataResolver->resolve($page, $author);
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
