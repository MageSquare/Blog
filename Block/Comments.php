<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block;

use MageSquare\Blog\Block\Comments\Message;
use MageSquare\Blog\Helper\Data;
use MageSquare\Blog\Helper\Settings;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\Comments as CommentModel;
use MageSquare\Blog\Model\ConfigProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

class Comments extends Template implements IdentityInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Settings
     */
    private $settingsHelper;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var \MageSquare\Blog\Helper\Date
     */
    private $dateHelper;

    /**
     * @var \MageSquare\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Helper\Date $dateHelper,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        Registry $registry,
        \MageSquare\Blog\Api\CommentRepositoryInterface $commentRepository,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->settingsHelper = $settingsHelper;
        $this->dataHelper = $dataHelper;
        $this->dateHelper = $dateHelper;
        $this->commentRepository = $commentRepository;
        $this->sessionFactory = $sessionFactory;
        $this->configProvider = $configProvider;
    }

    /**
     * @return \MageSquare\Blog\Model\Posts|bool|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPost()
    {
        $result = false;
        $parent = $this->getParentBlock();

        if ($parent) {
            if ($parent instanceof \MageSquare\Blog\Block\Content\Post) {
                $result = $parent->getPost();
            }
        }

        if (!$result) {
            $result = $this->getData('post') ?: $this->registry->registry(Registry::CURRENT_POST);
        }

        return $result;
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Comments\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCollection()
    {
        $comments = $this->commentRepository->getCommentsInPost($this->getPostId());

        $comments->addActiveFilter(
            $this->settingsHelper->getCommentsAutoapprove()
                ? null
                : $this->getSession()->getSessionId()
        );

        $comments->setDateOrder(\Magento\Framework\DB\Select::SQL_ASC)->setNotReplies();

        return $comments;
    }

    /**
     * @param CommentModel $message
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMessageHtml(CommentModel $message)
    {
        $result = false;
        $messageBlock = $this->getLayout()->getBlock(Message::AMBLOG_COMMENTS_MESSAGE);
        if (!$messageBlock) {
            $messageBlock = $this->getLayout()
                ->createBlock(\MageSquare\Blog\Block\Comments\Message::class, Message::AMBLOG_COMMENTS_MESSAGE)
                ->setTemplate("MageSquare_Blog::comments/list/message.phtml");
        }
        if ($messageBlock) {
            $messageBlock->setMessage($message);
            $result = $messageBlock->toHtml();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getFormUrl()
    {
        return $this->getUrl(
            'msblog/index/form',
            [
                'reply_to' => '{{reply_to}}',
                'post_id' => '{{post_id}}',
                'session_id' => '{{session_id}}',
            ]
        );
    }

    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('msblog/index/updateComments');
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPostId()
    {
        return (int)$this->getPost()->getId();
    }

    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl(
            'msblog/index/postForm',
            [
                'reply_to' => '{{reply_to}}',
                'post_id' => '{{post_id}}',
            ]
        );
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingsHelper()->getIconColorClass();
    }

    /**
     * @return bool
     */
    public function commentsEnabled()
    {
        return $this->settingsHelper->getUseComments();
    }

    /**
     * @return \MageSquare\Blog\Helper\Date
     */
    public function getDateHelper()
    {
        return $this->dateHelper;
    }

    /**
     * @return Data
     */
    public function getDataHelper()
    {
        return $this->dataHelper;
    }

    /**
     * @return Settings
     */
    public function getSettingsHelper()
    {
        return $this->settingsHelper;
    }

    /**
     * @return \Magento\Customer\Model\Session|\Magento\Customer\Model\Session\Proxy
     */
    public function getSession()
    {
        return $this->sessionFactory->create();
    }

    /**
     * @return Registry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * @return \MageSquare\Blog\Api\CommentRepositoryInterface
     */
    public function getRepository()
    {
        return $this->commentRepository;
    }

    /**
     * @return ConfigProvider
     */
    public function getConfigProvider(): ConfigProvider
    {
        return $this->configProvider;
    }

    /**
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdentities(): array
    {
        $comments = $this->getCollection()->getItems();

        return array_reduce($comments, function (array $carry, CommentModel $comment): array {
            return array_merge($carry, $comment->getIdentities());
        }, []);
    }
}
