<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Tags;

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
    const ADMIN_RESOURCE = 'MageSquare_Blog::tags';

    /**
     * @var \MageSquare\Blog\Model\Repository\TagRepository
     */
    private $repository;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        LoggerInterface $logger,
        \MageSquare\Blog\Model\Repository\TagRepository $repository
    ) {
        parent::__construct($context, $filter, $logger);
        $this->repository = $repository;
    }

    /**
     * @return \MageSquare\Blog\Model\Repository\TagRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Tag\Collection
     */
    public function getCollection()
    {
        return $this->repository->getTagCollection();
    }
}
