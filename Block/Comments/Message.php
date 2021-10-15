<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Comments;

use MageSquare\Blog\Model\Source\CommentStatus;

class Message extends \MageSquare\Blog\Block\Comments
{
    const AMBLOG_COMMENTS_MESSAGE = 'msblog_comments_message';

    /**
     * @return \MageSquare\Blog\Model\Comments
     */
    public function getMessage()
    {
        return $this->getData('message');
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->getMessage()->getMessage();
    }

    /**
     * @return int
     */
    public function getMessageId()
    {
        return (int)$this->getMessage()->getId();
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->getMessage()->getName() ?: __('Guest');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getDate()
    {
        $date = $this->getDateHelper()->renderDate($this->getMessage()->getCreatedAt());
        $time = $this->getDateHelper()->renderTime($this->getMessage()->getCreatedAt());

        return __("%1 at %2", $date, $time);
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Comments\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getRepliesCollection()
    {
        $activeFilter = $this->getSettingsHelper()->getCommentsAutoapprove()
            ? null
            : $this->getSession()->getSessionId();
        $messageId = $this->getMessage()->getId();

        return $this->getRepository()->getRepliesCollection($activeFilter, $messageId);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRepliesHtml()
    {
        $html = "";
        foreach ($this->getRepliesCollection() as $message) {
            $messageBlock = $this->getLayout()->getBlock(self::AMBLOG_COMMENTS_MESSAGE);
            if ($messageBlock) {
                $messageBlock->setMessage($message);
                $html .= $messageBlock->toHtml();
            }
        }

        return $html;
    }

    /**
     * @return bool
     */
    public function getNeedApproveMessage()
    {
        return $this->getMessage()->getStatus() == CommentStatus::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isMyComment()
    {
        $result = false;
        if ($this->getMessage()) {
            $message = $this->getMessage();
            $session = $this->getSession();
            $result = $session->isLoggedIn()
                ? $session->getCustomerId() == $message->getCustomerId()
                : $session->getSessionId() == $message->getSessionId();
        }

        return $result;
    }
}
