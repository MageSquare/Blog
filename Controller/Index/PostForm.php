<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Controller\Index;

use MageSquare\Blog\Helper\Settings;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\Source\CommentStatus;
use Magento\Framework\App\Action;

class PostForm extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var \MageSquare\Blog\Api\PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var Settings
     */
    private $settingsHelper;

    /**
     * @var \MageSquare\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var Action\Context
     */
    private $context;

    /**
     * @var \MageSquare\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var \MageSquare\Blog\Model\Notification\Notification
     */
    private $notificationModel;

    /**
     * @var \MageSquare\Blog\Block\Comments\Form
     */
    private $form;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        Registry $registry,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        \MageSquare\Blog\Api\CommentRepositoryInterface $commentRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \MageSquare\Blog\Helper\Url $urlHelper,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \MageSquare\Blog\Model\Notification\Notification $notificationModel,
        \MageSquare\Blog\Block\Comments\Form $form
    ) {
        parent::__construct($context);
        $this->storeManagerInterface = $storeManagerInterface;
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->sessionFactory = $sessionFactory;
        $this->settingsHelper = $settingsHelper;
        $this->commentRepository = $commentRepository;
        $this->context = $context;
        $this->urlHelper = $urlHelper;
        $this->objectFactory = $objectFactory;
        $this->notificationModel = $notificationModel;
        $this->form = $form;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [];

        $postData = $this->getRequest()->getPost()->toArray() ? : $this->getRequest()->getParams();
        $postData['store_id'] = (int)$this->storeManagerInterface->getStore()->getId();
        $postDataObject = $this->objectFactory->create(['data' => $postData]);

        if ($postId = (int)$this->getRequest()->getParam('post_id')) {
            try {
                $postInstance = $this->postRepository->getById($postId);
                $this->registry->unregister(Registry::CURRENT_POST);
                $this->registry->register(Registry::CURRENT_POST, $postInstance);

                $postDataObject->setPostId($postId);
                if ($this->getCustomerSession()->getCustomer()->getEntityId()
                    || $this->settingsHelper->getCommentsAllowGuests()
                ) {
                    $newComment = null;

                    $commentModel = $this->commentRepository->getComment();
                    $replyTo = (int)$postDataObject->getReplyTo();
                    if ($replyTo) {
                        try {
                            $newComment = $this->comment($postDataObject->getData(), $commentModel, $postInstance);
                        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                            $this->context->getMessageManager()
                                ->addErrorMessage(__('The message for reply wasn`t found'));
                            $result['error'] = $e->getMessage();
                        }
                    } else {
                        $postDataObject->unsetData('reply_to');
                        $newComment = $this->comment($postDataObject->getData(), $commentModel, $postInstance);
                    }

                    if ($newComment) {
                        $message = $this->_view->getLayout()
                            ->createBlock(\MageSquare\Blog\Block\Comments\Message::class)
                            ->setTemplate('MageSquare_Blog::comments/list/message.phtml');
                        if ($message) {
                            $this->form->setIsAjaxRendering(true);
                            $message->setMessage($newComment);
                            $message->setIsAjax(true);
                            $result['message'] = $message->toHtml();
                            $result['comment_id'] = $newComment->getId();
                            $result['form'] = $this->form->toHtml();
                        }
                    } else {
                        $result['error'] = __('Can not create comment.');
                        $this->context->getMessageManager()->addErrorMessage(__('Can not create comment.'));
                    }
                } else {
                    $this->context->getMessageManager()->addErrorMessage(
                        __('Your session was expired. Please refresh this page and try again.')
                    );
                }
            } catch (\Exception $e) {
                $this->context->getMessageManager()->addErrorMessage(__('Post is not found.'));
                $result['error'] = $e->getMessage();
            }
        }

        if ($this->getRequest()->getParam('is_amp')) {
            $refererUrl = $this->_redirect->getRefererUrl();

            if (strpos($refererUrl, 'amcomment') === false) {
                $refererUrl .= strpos($refererUrl, '?') !== false ? '&amcomment=1' : '?amcomment=1';
            }
            $this->_redirect($refererUrl);
        } else {
            $this->ajaxResponse($result);
        }
    }

    /**
     * @param array $data
     * @param \MageSquare\Blog\Model\Comments $comment
     * @param \MageSquare\Blog\Api\Data\PostInterface $postInstance
     * @return \MageSquare\Blog\Model\Comments
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function comment(array $data, $comment, $postInstance)
    {
        $storeId = (int) $comment->getCurrentStoreId();
        $comment->addData($data);
        $comment->setStoreId($storeId);
        if ($this->settingsHelper->getCommentsAutoapprove()) {
            $comment->setStatus(CommentStatus::STATUS_APPROVED);
            $comment->setSessionId(null);
        } else {
            $comment->setStatus(CommentStatus::STATUS_PENDING);
        }

        $customer = $this->getCustomerSession()->getCustomer();
        if (!$comment->getName() && $customer->getEntityId()) {
            $comment->setName($customer->getName() ?: null);
        }
        if (!$comment->getEmail() && $customer->getEntityId()) {
            $comment->setEmail($customer->getEmail() ?: null);
        }

        $comment->setMessage($this->prepareComment(isset($data['message']) ? $data['message'] : ''));
        $this->commentRepository->save($comment);
        $this->_eventManager->dispatch(
            'custom_checkbox_confirm_log',
            ['customer' => $this->sessionFactory->create()->getCustomer()]
        );
        try {
            $this->notificationModel->commentNotificationForAdmin($storeId, $comment, $postInstance);
        } catch (\Exception $exception) {
            $this->context->getMessageManager()->addErrorMessage(
                __('Can not send email notification.')
            );
        }

        return $comment;
    }

    /**
     * @param $message
     * @return string
     */
    private function prepareComment($message)
    {
        $message = htmlspecialchars_decode($message);
        $message = strip_tags($message);
        $message = trim($message);

        return $message;
    }

    /**
     * @param array $result
     */
    private function ajaxResponse($result = [])
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody(\Zend_Json::encode($result));
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }
}
