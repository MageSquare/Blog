<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml;

use MageSquare\Blog\Model\ImageProcessor;
use Magento\Framework\App\Request\DataPersistorInterface;

abstract class Authors extends \Magento\Backend\App\Action
{
    /**
     * @var \MageSquare\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \MageSquare\Blog\Model\BlogRegistry
     */
    private $blogRegistry;

    /**
     * @var ImageProcessor
     */
    protected $imageProcessor;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \MageSquare\Blog\Api\AuthorRepositoryInterface $authorRepository,
        DataPersistorInterface $dataPersistor,
        \Psr\Log\LoggerInterface $logger,
        \MageSquare\Blog\Model\BlogRegistry $blogRegistry,
        ImageProcessor $imageProcessor
    ) {
        parent::__construct($context);
        $this->authorRepository = $authorRepository;
        $this->dataPersistor = $dataPersistor;
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->blogRegistry = $blogRegistry;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageSquare_Blog::authors');
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return DataPersistorInterface
     */
    public function getDataPersistor()
    {
        return $this->dataPersistor;
    }

    /**
     * @return \MageSquare\Blog\Model\BlogRegistry
     */
    public function getRegistry()
    {
        return $this->blogRegistry;
    }

    /**
     * @return \MageSquare\Blog\Api\AuthorRepositoryInterface
     */
    public function getAuthorRepository()
    {
        return $this->authorRepository;
    }

    /**
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getPageFactory()
    {
        return $this->resultPageFactory;
    }
}
