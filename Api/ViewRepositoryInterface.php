<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

/**
 * @api
 */
interface ViewRepositoryInterface
{
    /**
     * @param int $postId
     *
     * @return int
     */
    public function getViewCountByPostId($postId);

    /**
     * @param int $postId
     * @param string|null $refererUrl
     *
     * @return bool true on success
     */
    public function create($postId, $refererUrl = null);

    /**
     * Save
     *
     * @param \MageSquare\Blog\Api\Data\ViewInterface $view
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     */
    public function save(\MageSquare\Blog\Api\Data\ViewInterface $view);

    /**
     * Get by id
     *
     * @param int $viewId
     *
     * @return \MageSquare\Blog\Api\Data\ViewInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($viewId);

    /**
     * Delete
     *
     * @param \MageSquare\Blog\Api\Data\ViewInterface $view
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSquare\Blog\Api\Data\ViewInterface $view);

    /**
     * Delete by id
     *
     * @param int $viewId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($viewId);
}
