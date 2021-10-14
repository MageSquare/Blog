<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block;

use Magento\Widget\Block\BlockInterface;

class Featured extends \Magento\Framework\View\Element\Template implements BlockInterface
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $collection;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    protected $_template = 'MageSquare_Blog::featured.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        array $data = []
    ) {
        $this->postRepository = $postRepository;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        return strpos($this->getRequest()->getPathInfo(), '/amp/') === false ? parent::toHtml() : '';
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $collection = $this->postRepository->getFeaturedPosts($this->storeManager->getStore()->getId());
            $this->collection = $collection;
        }

        return $this->collection;
    }
}
