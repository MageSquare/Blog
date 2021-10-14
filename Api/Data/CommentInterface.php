<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api\Data;

interface CommentInterface
{
    const COMMENT_ID = 'comment_id';

    const POST_ID = 'post_id';

    const STORE_ID = 'store_id';

    const STATUS = 'status';

    const CUSTOMER_ID = 'customer_id';

    const REPLY_TO = 'reply_to';

    const MESSAGE = 'message';

    const NAME = 'name';

    const EMAIL = 'email';

    const SESSION_ID = 'session_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getCommentId();

    /**
     * @param int $commentId
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setCommentId($commentId);

    /**
     * @return int
     */
    public function getPostId();

    /**
     * @param int $postId
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setPostId($postId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setStoreId($storeId);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setStatus($status);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int|null $customerId
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return int|null
     */
    public function getReplyTo();

    /**
     * @param int|null $replyTo
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setReplyTo($replyTo);

    /**
     * @return string|null
     */
    public function getMessage();

    /**
     * @param string|null $message
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setMessage($message);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getEmail();

    /**
     * @param string|null $email
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setEmail($email);

    /**
     * @return string|null
     */
    public function getSessionId();

    /**
     * @param string|null $sessionId
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setSessionId($sessionId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \MageSquare\Blog\Api\Data\CommentInterface
     */
    public function setUpdatedAt($updatedAt);
}
