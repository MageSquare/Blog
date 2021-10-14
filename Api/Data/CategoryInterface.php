<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api\Data;

interface CategoryInterface
{
    const ROUTE_CATEGORY = 'category';

    const CATEGORY_ID = 'category_id';

    const NAME = 'name';

    const URL_KEY = 'url_key';

    const STATUS = 'status';

    const SORT_ORDER = 'sort_order';

    const META_TITLE = 'meta_title';

    const META_TAGS = 'meta_tags';

    const META_DESCRIPTION = 'meta_description';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const PARENT_ID = 'parent_id';

    const PATH = 'path';

    const LEVEL = 'level';

    const ROOT_CATEGORY_ID = "0";

    const STORE_ID = "store_id";

    const DESCRIPTION = "description";

    const FIELDS_BY_STORE = [
        'general' => [
            self::NAME,
            self::STATUS,
            self::URL_KEY,
            self::DESCRIPTION,
        ],
        'meta_data' => [
            self::META_TITLE,
            self::META_TAGS,
            self::META_DESCRIPTION
        ]
    ];

    /**
     * @return int
     */
    public function getCategoryId();

    /**
     * @param int $categoryId
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setCategoryId($categoryId);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $urlKey
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setSortOrder($sortOrder);

    /**
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * @param string|null $metaTitle
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string|null
     */
    public function getMetaTags();

    /**
     * @param string|null $metaTags
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setMetaTags($metaTags);

    /**
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * @param string|null $metaDescription
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $parentId
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setParentId($parentId);

    /**
     * @return string
     */
    public function getPath();

    /**
     * @param string $path
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setPath($path);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param int $level
     *
     * @return \MageSquare\Blog\Api\Data\CategoryInterface
     */
    public function setLevel($level);

    /**
     * @return bool
     */
    public function hasChildren();

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Categories\Collection
     */
    public function getChildrenCollection();

    /**
     * @return bool
     */
    public function hasActiveChildren();

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @param string|null $description
     */
    public function setDescription(string $description): void;
}
