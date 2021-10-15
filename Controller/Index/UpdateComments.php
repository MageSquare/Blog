<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Index;

use MageSquare\Blog\Api\PostRepositoryInterface;
use MageSquare\Blog\Block\Comments;
use MageSquare\Blog\Block\Comments\Form as CommentsFormBlock;
use MageSquare\Blog\Model\Blog\Registry;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\DesignLoader;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;

/**
 * Class
 */
class UpdateComments implements HttpPostActionInterface
{
    const POST_ID = 'post_id';
    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var Validator
     */
    private $formKeyValidator;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var DesignLoader
     */
    private $designLoader;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var JsonFactory
     */
    private $resultFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        PostRepositoryInterface $postRepository,
        Validator $formKeyValidator,
        Registry $registry,
        DesignLoader $designLoader,
        LayoutFactory $layoutFactory,
        JsonFactory $resultFactory,
        RequestInterface $request,
        SessionFactory $sessionFactory
    ) {
        $this->postRepository = $postRepository;
        $this->formKeyValidator = $formKeyValidator;
        $this->registry = $registry;
        $this->layoutFactory = $layoutFactory;
        $this->designLoader = $designLoader;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [];

        try {
            if (!$this->formKeyValidator->validate($this->getRequest())) {
                throw new LocalizedException(
                    __('We can\'t load comments right now. Please reload the page.')
                );
            }

            $this->prepareEnvironment();

            $result = [
                'content' => $this->getCommentsListHtml(),
                'short_content' => $this->getShortContentHtml()
            ];

            if ($this->isNeedRenderCommentsForm()) {
                $result['comments_form'] = $this->getCommentsFormHtml();
            }
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $resultJson = $this->resultFactory->create();

        return $resultJson->setData($result);
    }

    private function getLayout(): LayoutInterface
    {
        if ($this->layout === null) {
            $this->layout = $this->layoutFactory->create();
        }

        return $this->layout;
    }

    private function getCommentsListHtml(): string
    {
        return $this->getLayout()
            ->createBlock(Comments::class)
            ->setTemplate("MageSquare_Blog::comments/list.phtml")
            ->toHtml();
    }

    private function getShortContentHtml(): string
    {
        return $this->getLayout()
            ->createBlock(\MageSquare\Blog\Block\Content\Post\Details::class)
            ->setTemplate("MageSquare_Blog::post/short_comments.phtml")
            ->toHtml();
    }

    private function getCommentsFormHtml(): string
    {
        /** @var CommentsFormBlock $commentsFormBlock **/
        $commentsFormBlock = $this->getLayout()->createBlock(CommentsFormBlock::class);
        $commentsFormBlock->setIsAjaxRendering(true);

        return $commentsFormBlock->toHtml();
    }

    private function prepareEnvironment(): void
    {
        $postId = (int)$this->getRequest()->getParam(self::POST_ID);
        $post = $this->postRepository->getById($postId);
        $this->registry->register(Registry::CURRENT_POST, $post, true);
        $this->designLoader->load();
    }

    private function getRequest(): RequestInterface
    {
        return $this->request;
    }

    private function isNeedRenderCommentsForm(): bool
    {
        return $this->sessionFactory->create()->isLoggedIn();
    }
}
