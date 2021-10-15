<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\ResourceModel;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Api\TagRepositoryInterface;
use MageSquare\Blog\Helper\Data as BlogHelper;
use MageSquare\Blog\Model\ImageProcessor;
use MageSquare\Blog\Model\Posts as PostModel;
use MageSquare\Blog\Model\ResourceModel\Posts\Save\SavePartInterface;
use MageSquare\Blog\Model\ResourceModel\Posts\Save\SavePartProcessorsPool;
use MageSquare\Blog\Model\Source\PostStatus;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class
 */
class Posts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const TABLE_NAME = 'magesquare_blog_posts';

    const POSTS_TAGS_RELATION_TABLE = 'magesquare_blog_posts_tag';

    const POSTS_STORE_TABLE = 'magesquare_blog_posts_store';

    /**
     * @var TagRepositoryInterface
     */
    private $tagRepository;

    /**
     * @var \MageSquare\Blog\Api\Data\TagInterface
     */
    private $tagModel;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var BlogHelper
     */
    private $helper;

    /**
     * @var SavePartProcessorsPool
     */
    private $savePartProcessorsPool;

    public function __construct(
        Context $context,
        TagRepositoryInterface $tagRepository,
        AuthorRepositoryInterface $authorRepository,
        ImageProcessor $imageProcessor,
        BlogHelper $helper,
        SavePartProcessorsPool $savePartProcessorsPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->tagRepository = $tagRepository;
        $this->tagModel = $tagRepository->getTagModel();
        $this->authorRepository = $authorRepository;
        $this->imageProcessor = $imageProcessor;
        $this->helper = $helper;
        $this->savePartProcessorsPool = $savePartProcessorsPool;
    }

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, PostInterface::POST_ID);
    }

    /**
     * @param PostInterface $object
     * @return Posts|\Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterLoad($object);

        return $this->loadTags($object)->loadCategories($object)->loadStores($object);
    }

    /**
     * @param PostInterface $postModel
     *
     * @return $this
     */
    private function loadTags(PostInterface $postModel)
    {
        $postId = $postModel->getId();
        if ($postId) {
            $select = $this->getConnection()->select()
                ->from(
                    ['posts_tags' => $this->getTable(Posts::POSTS_TAGS_RELATION_TABLE)],
                    ['posts_tags.tag_id', 'tags.name']
                )
                ->joinLeft(
                    ['tags' => $this->getTable('magesquare_blog_tags_store')],
                    'posts_tags.tag_id = tags.tag_id',
                    []
                )
                ->where('posts_tags.post_id = :post_id');

            $tags = $this->getConnection()->fetchPairs($select, [':post_id' => $postId]);
            $postModel->setData(PostInterface::TAGS, implode(',', array_values($tags)));
            $postModel->setData(PostInterface::TAG_IDS, implode(',', array_keys($tags)));
        }

        return $this;
    }

    /**
     * @param PostInterface $postModel
     *
     * @return $this
     */
    private function loadStores(PostInterface $postModel)
    {
        $postId = $postModel->getId();
        if ($postId) {
            $select = $this->getConnection()->select()
                ->from(
                    [$this->getTable(Posts::POSTS_STORE_TABLE)],
                    ['store_id']
                )->where('post_id = :post_id');

            $stores = $this->getConnection()->fetchCol($select, [':post_id' => $postId]);
            $postModel->setData(PostInterface::STORES, $stores);
        }

        return $this;
    }

    /**
     * @param PostInterface $postModel
     *
     * @return $this
     */
    private function loadCategories(PostInterface $postModel)
    {
        $postId = $postModel->getId();
        if ($postId) {
            $select = $this->getConnection()->select()
                ->from(
                    ['posts_cats' => $this->getTable('magesquare_blog_posts_category')],
                    ['category_id']
                )->joinLeft(
                    ['cats' => $this->getTable('magesquare_blog_categories')],
                    'posts_cats.category_id = cats.category_id',
                    []
                )->where('posts_cats.post_id = :post_id');

            $categories = $this->getConnection()->fetchCol($select, [':post_id' => $postId]);
            $postModel->setData(PostInterface::CATEGORIES, $categories);
        }

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        try {
            $this->validatePost($object);
        } catch (AlreadyExistsException $e) {
            $this->helper->getLogger()->critical($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }

        $this->deleteOldImage($object, PostModel::POST_THUMBNAIL);
        $this->deleteOldImage($object, PostModel::LIST_THUMBNAIL);

        return parent::_beforeSave($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @throws AlreadyExistsException
     * @throws LocalizedException
     */
    protected function validatePost(AbstractModel $object)
    {
        if (!$this->validateUrlKey($object) && ($object->getStatus() != PostStatus::STATUS_DISABLED)) {
            $object->setStatus(PostStatus::STATUS_DISABLED);
            throw new AlreadyExistsException(
                __(
                    "Post '%1' can be disabled only. Some post has same Url Key for the same Store View.",
                    $object->getTitle()
                )
            );
        }
    }

    /**
     * @param $object
     * @param $fieldName
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function deleteOldImage($object, $fieldName)
    {
        $fileName = $fieldName . '_file';
        if ($object->getOrigData($fieldName)
            && $object->getOrigData($fieldName) != $object->getData($fieldName)
        ) {
            $this->imageProcessor->deleteImage($object->getOrigData($fieldName));
        }

        if (($image = $object->getData($fileName)) && !empty($image['delete'])) {
            $this->imageProcessor->deleteImage($image[0]['name']);
            $object->setIcon(null);
        }
    }

    /**
     * @param AbstractModel $post
     * @return bool
     * @throws AlreadyExistsException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function validateUrlKey(PostInterface $post)
    {
        $stores = $post->getStores();
        if (!is_array($stores)) {
            $stores = [$stores];
        }

        $connection = $this->getConnection();
        $bind = [PostInterface::URL_KEY => $post->getUrlKey()];

        $select = $connection->select()->from(
            ['main_table' => $this->getMainTable()],
            [$this->getIdFieldName()]
        )->joinLeft(
            ['store' => $this->getTable('magesquare_blog_posts_store')],
            'main_table.post_id = store.post_id',
            ['store.store_id']
        )->where(
            'main_table.url_key = :url_key'
        );

        if ($post->getPostId()) {
            $bind['post_id'] = (int)$post->getPostId();
            $select->where('store.post_id != :post_id');
        }

        $bind['store_id'] = implode(', ', $stores);
        $select->where('store.store_id IN (:store_id)');

        $result = $connection->fetchOne($select, $bind);

        if ($result !== false) {
            throw new AlreadyExistsException(
                __('A post with the same url key already exists.')
            );
        }

        return true;
    }

    /**
     * @param PostModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        $connection = $this->getConnection();
        $this->saveTags($object, $connection);
        $this->saveStores($object, $connection);
        $this->saveCategories($object, $connection);
        $this->saveImage($object, PostModel::POST_THUMBNAIL);
        $this->saveImage($object, PostModel::LIST_THUMBNAIL);

        /** @var SavePartInterface $savePartProcessor **/
        foreach ($this->savePartProcessorsPool as $savePartProcessor) {
            $savePartProcessor->execute($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param $object
     * @param $name
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveImage($object, $name)
    {
        $image = $object->getData($name . '_file');
        if ($image && isset($image[0]['size'])) {
            $this->imageProcessor->processCategoryIcon($object->getData($name));
        }
    }

    /**
     * @param PostInterface $post
     * @param AdapterInterface $connection
     *
     * @return $this
     */
    private function saveStores(PostInterface $post, AdapterInterface $connection)
    {
        $stores = $post->getStores();
        if (!empty($stores)) {
            $condition = [PostInterface::POST_ID . ' = ?' => $post->getPostId()];
            $connection->delete($this->getTable('magesquare_blog_posts_store'), $condition);

            $insertedStoreIds = [];
            foreach ($stores as $storeId) {
                if (in_array($storeId, $insertedStoreIds)) {
                    continue;
                }

                $insertedStoreIds[] = $storeId;
                $storeInsert = ['store_id' => $storeId, 'post_id' => $post->getPostId()];
                $connection->insert($this->getTable('magesquare_blog_posts_store'), $storeInsert);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $post
     * @param AdapterInterface $connection
     *
     * @return $this
     */
    private function saveTags(PostInterface $post, AdapterInterface $connection)
    {
        $tags = $post->getData('tags');
        $condition = [PostInterface::POST_ID . ' = ?' => $post->getPostId()];
        $connection->delete($this->getTable('magesquare_blog_posts_tag'), $condition);
        if (!empty($tags)) {
            $tagsArray = explode(',', $tags);
            $tagsList = $this->tagRepository->getList($tagsArray);
            $existTags = [];
            foreach ($tagsList as $tag) {
                $existTags[$tag->getId()] = $tag->getName();
            }

            foreach ($tagsArray as $tag) {
                if (!$tag) {
                    continue;
                }
                //insert exist tag or create new tag
                $this->tagModel->setData([])->isObjectNew(false);
                if (in_array($tag, $existTags)) {
                    $tagInsert = ['tag_id' => array_search($tag, $existTags), 'post_id' => $post->getPostId()];
                } else {
                    $newTag = [
                        TagInterface::NAME => $tag,
                        TagInterface::STORE_ID => \Magento\Store\Model\Store::DEFAULT_STORE_ID
                    ];
                    $this->tagModel->setData($newTag);
                    $this->tagRepository->save($this->tagModel);
                    $id = $this->tagModel->getId();
                    $tagInsert = ['tag_id' => $id, 'post_id' => $post->getPostId()];
                }

                $connection->insert($this->getTable('magesquare_blog_posts_tag'), $tagInsert);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $post
     * @param AdapterInterface $connection
     *
     * @return $this
     */
    private function saveCategories(PostInterface $post, AdapterInterface $connection)
    {
        $categories = $post->getCategories();
        $condition = [PostInterface::POST_ID . ' = ?' => $post->getPostId()];
        $connection->delete($this->getTable('magesquare_blog_posts_category'), $condition);
        if (!empty($categories)) {
            $insertedIds = [];
            $categories = is_string($categories) ? explode(',', $categories) : $categories;
            foreach ($categories as $catId) {
                if (!$catId || in_array($catId, $insertedIds)) {
                    continue;
                }
                $insertedIds[] = $catId;
                $insert = ['category_id' => $catId, 'post_id' => $post->getId()];
                $connection->insert($this->getTable('magesquare_blog_posts_category'), $insert);
            }
        }

        return $this;
    }

    /**
     * @param PostInterface $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $postThumbnail = $object->getPostThumbnail();
        $listThumbnail = $object->getListThumbnail();
        if ($postThumbnail) {
            $this->imageProcessor->deleteImage($postThumbnail);
        }

        if ($listThumbnail) {
            $this->imageProcessor->deleteImage($listThumbnail);
        }

        return parent::_afterDelete($object);
    }
}
