<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Comments;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractMassAction
 */
abstract class AbstractMassAction extends \MageSquare\Blog\Controller\Adminhtml\AbstractMassAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'MageSquare_Blog::comments';

    /**
     * @var \MageSquare\Blog\Model\Repository\CommentRepository
     */
    private $repository;

    /**
     * @var \MageSquare\Blog\Model\ResourceModel\Comments\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \MageSquare\Blog\Model\Repository\CommentRepository $repository,
        \MageSquare\Blog\Model\ResourceModel\Comments\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context, $filter, $logger);
        $this->repository = $repository;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Comments\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return \MageSquare\Blog\Model\Repository\CommentRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
