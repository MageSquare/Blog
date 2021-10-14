<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use MageSquare\Blog\Model\ResourceModel\Posts as PostsResource;

/**
 * @api
 */
interface PostRepositoryInterface
{
    /**
     * Save
     *
     * @param \MageSquare\Blog\Api\Data\PostInterface $post
     *
     * @return \MageSquare\Blog\Api\Data\PostInterface
     */
    public function save(\MageSquare\Blog\Api\Data\PostInterface $post);

    /**
     * Get by id
     *
     * @param int $postId
     *
     * @return \MageSquare\Blog\Api\Data\PostInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postId);

    /**
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\PostInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\PostInterface
     */
    public function getByUrlKeyWithAllStatuses($urlKey);

    /**
     * @return \MageSquare\Blog\Api\Data\PostInterface
     */
    public function getPost();

    /**
     * Delete
     *
     * @param \MageSquare\Blog\Api\Data\PostInterface $post
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSquare\Blog\Api\Data\PostInterface $post);

    /**
     * Delete by id
     *
     * @param int $postId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($postId);

    /**
     * @param int $tagId
     *
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getTaggedPosts($tagId);

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection;
     */
    public function getPostCollection();

    /**
     * @param $page
     * @return PostsResource\Collection
     */
    public function getPostsByPage($page);

    /**
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getRecentPosts();

    /**
     * @param null $storeId
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getActivePosts($storeId = null);

    /**
     * @param null $storeId
     * @return PostsResource\Collection
     * @throws NoSuchEntityException
     */
    public function getFeaturedPosts($storeId = null);
}
