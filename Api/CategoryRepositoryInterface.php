<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Model\ResourceModel\Categories\Collection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

/**
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Save
     *
     * @param \MageSquare\Blog\Api\Data\CategoryInterface $category
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function save(\MageSquare\Blog\Api\Data\CategoryInterface $category);

    /**
     * Get by id
     *
     * @param int $categoryId
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($categoryId);

    /**
     * @deprecared since version 2.7.0 Now url key can be configured by store view
     *
     * @see \MageSquare\Blog\Api\CategoryRepositoryInterface::getByUrlKeyAndStoreId
     *
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function getByUrlKey($urlKey);

    /**
     * @param string|null $urlKey
     * @param int $storeId
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function getByUrlKeyAndStoreId(?string $urlKey, int $storeId = Store::DEFAULT_STORE_ID): CategoryInterface;

    /**
     * @return \MageSquare\Blog\Model\Categories
     */
    public function getCategory();

    /**
     * Delete
     *
     * @param \MageSquare\Blog\Api\Data\CategoryInterface $category
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSquare\Blog\Api\Data\CategoryInterface $category);

    /**
     * Delete by id
     *
     * @param int $categoryId
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($categoryId);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * @return \Magento\Framework\DataObject[]
     */
    public function getAllCategories();

    /**
     * @param $categoryId
     *
     * @return array
     */
    public function getStores($categoryId);

    /**
     * @param $postId
     * @return Collection
     */
    public function getCategoriesByPost($postId);

    /**
     * @param null $storeId
     * @return Collection
     */
    public function getActiveCategories($storeId = null);

    /**
     * @param array $categoryIds
     * @return Collection
     */
    public function getCategoriesByIds($categoryIds = []);

    /**
     * @param $parentId
     * @param $limit
     * @param $storeId
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getChildrenCategories($parentId, $limit = 0, $storeId = null);

    /**
     * @param $categoryId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($categoryId, $storeId = 0, $isAddDefaultStore = true);
}
