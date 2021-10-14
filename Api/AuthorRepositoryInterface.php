<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Model\ResourceModel\Author\Collection;
use Magento\Store\Model\Store;

/**
 * @api
 */
interface AuthorRepositoryInterface
{
    /**
     * Save
     *
     * @param \MageSquare\Blog\Api\Data\AuthorInterface $author
     *
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function save(\MageSquare\Blog\Api\Data\AuthorInterface $author);

    /**
     * Get by id
     *
     * @param int $authorId
     *
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($authorId);

    /**
     * @deprecared since version 2.7.0 Now url key can be configured by store view
     *
     * @see \MageSquare\Blog\Api\AuthorRepositoryInterface::getByUrlKeyAndStoreId
     *
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param string|null $urlKey
     * @param int $storeId
     *
     * @return AuthorInterface
     */
    public function getByUrlKeyAndStoreId(?string $urlKey, int $storeId = Store::DEFAULT_STORE_ID): AuthorInterface;

    /**
     * @param $name
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function getByName($name);

    /**
     * Delete
     *
     * @param \MageSquare\Blog\Api\Data\AuthorInterface $author
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSquare\Blog\Api\Data\AuthorInterface $author);

    /**
     * Delete by id
     *
     * @param int $authorId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($authorId);

    /**
     * Lists
     *
     * @param string[] $authors
     *
     * @return Collection
     */
    public function getList($authors);

    /**
     * @return \MageSquare\Blog\Model\Author
     */
    public function getAuthorModel();

    /**
     * @return Collection
     */
    public function getAuthorCollection();

    /**
     * @param string $name
     * @param string $facebookProfile
     * @param string $twitterProfile
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function createAuthor($name, $facebookProfile, $twitterProfile);

    /**
     * @param int $authorId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($authorId, $storeId = 0, $isAddDefaultStore = true);
}
