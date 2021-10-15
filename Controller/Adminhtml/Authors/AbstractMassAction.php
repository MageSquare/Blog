<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Authors;

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
    const ADMIN_RESOURCE = 'MageSquare_Blog::authors';

    /**
     * @var \MageSquare\Blog\Api\AuthorRepositoryInterface
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \MageSquare\Blog\Api\AuthorRepositoryInterface $repository
    ) {
        parent::__construct($context, $filter, $logger);
        $this->repository = $repository;
    }

    /**
     * @return \MageSquare\Blog\Api\AuthorRepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Author\Collection
     */
    public function getCollection()
    {
        return $this->repository->getAuthorCollection();
    }
}
