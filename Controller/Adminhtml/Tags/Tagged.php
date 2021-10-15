<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Tags;

use Magento\Backend\App\Action;

/**
 * Class
 */
class Tagged extends Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Registry $registry,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);

        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->coreRegistry = $registry;
        $this->postRepository = $postRepository;
    }

    public function execute()
    {
        $tagId = (int)$this->_request->getParam('id');
        if ($tagId) {
            $postsCollection = $this->postRepository->getTaggedPosts($tagId);
            $this->coreRegistry->register('magesquare_blog_current_posts', $postsCollection);
        }

        return $this->resultLayoutFactory->create();
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageSquare_Blog::tags');
    }
}
