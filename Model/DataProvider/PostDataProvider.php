<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\DataProvider;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Api\PostRepositoryInterface;
use MageSquare\Blog\Model\ImageProcessor;
use MageSquare\Blog\Model\Posts;
use MageSquare\Blog\Model\ResourceModel\Posts\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface as UiDataModifiersPool;

class PostDataProvider extends AbstractDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var PostRepositoryInterface
     */
    private $postRepository;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var AuthorRepositoryInterface
     */
    private $authorRepository;

    /**
     * @var UiDataModifiersPool
     */
    private $uiDataModifiersPool;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $collectionFactory,
        AuthorRepositoryInterface $authorRepository,
        PostRepositoryInterface $postRepository,
        ImageProcessor $imageProcessor,
        UiDataModifiersPool $uiDataModifiersPool,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->postRepository = $postRepository;
        $this->imageProcessor = $imageProcessor;
        $this->collectionFactory = $collectionFactory;
        $this->authorRepository = $authorRepository;
        $this->uiDataModifiersPool = $uiDataModifiersPool;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        $data = parent::getData();
        if ($data['totalRecords'] > 0) {
            $postId = (int)$data['items'][0]['post_id'];
            $model = $this->getPost($postId);
            if ($model) {
                $postData = $model->getData();
                $author = $this->getAuthor($postData);
                $postData[PostInterface::AUTHOR_ID] = $author ? $author->getId() : null;
                $postData = $this->modifyData($model, $postData);
                $data[$postId] = $postData;
            }
        }
        if ($savedData = $this->dataPersistor->get(Posts::PERSISTENT_NAME)) {
            $savedPostId = isset($savedData['post_id']) ? $savedData['post_id'] : null;
            $data[$savedPostId] = isset($data[$savedPostId])
                ? array_merge($data[$savedPostId], $savedData)
                : $savedData;
            $this->dataPersistor->clear(Posts::PERSISTENT_NAME);
        }

        return $data;
    }

    public function getMeta()
    {
        $meta = parent::getMeta();

        foreach ($this->uiDataModifiersPool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }

    /**
     * @param $postId
     *
     * @return PostInterface|null
     */
    private function getPost($postId)
    {
        try {
            $model = $this->postRepository->getById($postId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $model = null;
        }

        return $model;
    }

    /**
     * @param $postData
     *
     * @return \MageSquare\Blog\Api\Data\AuthorInterface|null
     */
    private function getAuthor($postData)
    {
        try {
            $authorId = (int)$postData[PostInterface::AUTHOR_ID];
            $model = $authorId ? $this->authorRepository->getById($authorId) : null;
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $model = null;
        }

        return $model;
    }

    /**
     * @param \MageSquare\Blog\Api\Data\PostInterface $model
     * @param array $postData
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function modifyData(\MageSquare\Blog\Api\Data\PostInterface $model, $postData)
    {
        $this->imageFormat($model->getPostThumbnail(), $postData, Posts::POST_THUMBNAIL);
        $this->imageFormat($model->getListThumbnail(), $postData, Posts::LIST_THUMBNAIL);

        if (isset($postData['related_post_ids']) && $postData['related_post_ids']) {
            $postData['related_post_ids'] = [
                'related_posts_container' => array_values($this->getPostsData($postData['related_post_ids']))
            ];
        }

        foreach ($this->uiDataModifiersPool->getModifiersInstances() as $modifier) {
            $postData = $modifier->modifyData($postData);
        }

        return $postData;
    }

    /**
     * @param $thumbnail
     * @param $categoryData
     * @param $fieldName
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function imageFormat($thumbnail, &$categoryData, $fieldName)
    {
        if ($thumbnail) {
            $categoryData[$fieldName . '_file'] = [
                [
                    'name' => $thumbnail,
                    'url'  => $this->imageProcessor->getThumbnailUrl($thumbnail, $fieldName)
                ]
            ];
        }
    }

    /**
     * @param array|string $postIds
     *
     * @return array
     */
    protected function getPostsData($postIds)
    {
        if (!is_array($postIds)) {
            $postIds = explode(',', $postIds);
        }
        $postCollection = $this->collectionFactory->create();
        $postCollection->addFieldToFilter('post_id', ['in' => $postIds]);

        $result = [];
        /** @var PostInterface $post */
        foreach ($postCollection->getItems() as $post) {
            $result[$post->getPostId()] = $this->fillData($post);
        }

        return $result;
    }

    /**
     * @param PostInterface $post
     *
     * @return array
     */
    protected function fillData(PostInterface $post)
    {
        return [
            'post_id'        => $post->getPostId(),
            'post_thumbnail' => $post->getListThumbnailSrc(),
            'title'          => $post->getTitle(),
            'url_key'        => $post->getUrlKey(),
            'status'         => $post->getStatus()
        ];
    }
}
