<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\ResourceModel\Tag\Collection;
use Magento\Store\Model\Store;

/**
 * @api
 */
interface TagRepositoryInterface
{
    /**
     * Save
     *
     * @param \MageSquare\Blog\Api\Data\TagInterface $tag
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function save(\MageSquare\Blog\Api\Data\TagInterface $tag);

    /**
     * Get by id
     *
     * @param int $tagId
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($tagId);

    /**
     * @deprecared since version 2.7.0 Now url key can be configured by store view
     *
     * @see \MageSquare\Blog\Api\TagRepositoryInterface::getByUrlKeyAndStoreId
     *
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param string|null $urlKey
     * @param int $storeId
     *
     * @return TagInterface
     */
    public function getByUrlKeyAndStoreId(?string $urlKey, int $storeId = Store::DEFAULT_STORE_ID): TagInterface;

    /**
     * Delete
     *
     * @param \MageSquare\Blog\Api\Data\TagInterface $tag
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSquare\Blog\Api\Data\TagInterface $tag);

    /**
     * Delete by id
     *
     * @param int $tagId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($tagId);

    /**
     * Lists
     *
     * @param string[] $tags
     *
     * @return Collection
     */
    public function getList($tags);

    /**
     * @return \MageSquare\Blog\Model\Tag
     */
    public function getTagModel();

    /**
     * @return Collection
     */
    public function getTagCollection();

    /**
     * @param int $postId
     * @param int $storeId
     * @return Collection
     */
    public function getTagsByPost($postId, $storeId);

    /**
     * @param array $tagsIds
     * @return Collection
     */
    public function getTagsByIds($tagsIds = []);

    /**
     * @param null $storeId
     * @return Collection
     */
    public function getActiveTags($storeId = null);

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getAllTags();

    /**
     * @param $tagId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($tagId, $storeId = 0, $isAddDefaultStore = true);
}
