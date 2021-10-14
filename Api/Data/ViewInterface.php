<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api\Data;

interface ViewInterface
{
    const VIEW_ID = 'view_id';

    const POST_ID = 'post_id';

    const CUSTOMER_ID = 'customer_id';

    const SESSION_ID = 'session_id';

    const REMOTE_ADDR = 'remote_addr';

    const STORE_ID = 'store_id';

    const CREATED_AT = 'created_at';

    const REFERER_URL = 'referer_url';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $viewId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setViewId($viewId);

    /**
     * @return int
     */
    public function getPostId();

    /**
     * @param int $postId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setPostId($postId);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int|null $customerId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setCustomerId($customerId);

    /**
     * @return string
     */
    public function getSessionId();

    /**
     * @param string $sessionId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setSessionId($sessionId);

    /**
     * @return int
     */
    public function getRemoteAddr();

    /**
     * @param int $remoteAddr
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setRemoteAddr($remoteAddr);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string|null
     */
    public function getRefererUrl();

    /**
     * @param string|null $refererUrl
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function setRefererUrl($refererUrl);
}
