<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Controller\AbstractController;

use MageSquare\Blog\Api\CategoryRepositoryInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver\Category as MetaResolver;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\UrlResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

class Category extends Action
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
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

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
        CategoryRepositoryInterface $categoryRepository,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->urlResolver = $urlResolver;
        $this->resultPageFactory = $resultPageFactory;
        $this->metaDataResolver = $metaDataResolver;
        $this->categoryRepository = $categoryRepository;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        if ($id) {
            try {
                $page = $this->resultPageFactory->create();
                $category = $this->categoryRepository->getByIdAndStore($id, $this->storeManager->getStore()->getId());
                $this->registry->register(Registry::CURRENT_CATEGORY, $category, true);

                $this->metaDataResolver->resolve($page, $category);
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
