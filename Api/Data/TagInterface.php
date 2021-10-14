<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api\Data;

interface TagInterface
{
    const ROUTE_TAG = 'tag';

    const TAG_ID = 'tag_id';

    const NAME = 'name';

    const URL_KEY = 'url_key';

    const META_TITLE = 'meta_title';

    const META_TAGS = 'meta_tags';

    const META_DESCRIPTION = 'meta_description';

    const STORE_ID = 'store_id';

    const FIELDS_BY_STORE = [
        'general' => [
            self::NAME,
            self::URL_KEY,
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
    public function getTagId();

    /**
     * @param int $tagId
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setTagId($tagId);

    /**
     * @return string|null
     */
    public function getName();

    /**
     * @param string|null $name
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setName($name);

    /**
     * @return string|null
     */
    public function getUrlKey();

    /**
     * @param string|null $urlKey
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setUrlKey($urlKey);

    /**
     * @return string|null
     */
    public function getMetaTitle();

    /**
     * @param string|null $metaTitle
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setMetaTitle($metaTitle);

    /**
     * @return string|null
     */
    public function getMetaTags();

    /**
     * @param string|null $metaTags
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setMetaTags($metaTags);

    /**
     * @return string|null
     */
    public function getMetaDescription();

    /**
     * @param string|null $metaDescription
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setMetaDescription($metaDescription);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return \MageSquare\Blog\Api\Data\TagInterface
     */
    public function setStoreId($storeId);
}
