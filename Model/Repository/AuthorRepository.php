<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Repository;

use MageSquare\Blog\Api\AuthorRepositoryInterface;
use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Model\AuthorFactory;
use MageSquare\Blog\Model\ResourceModel\Author as AuthorResource;
use MageSquare\Blog\Model\ResourceModel\Author\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;

class AuthorRepository implements AuthorRepositoryInterface
{
    /**
     * @var AuthorFactory
     */
    private $authorFactory;

    /**
     * @var AuthorResource
     */
    private $authorResource;

    /**
     * Model data storage
     *
     * @var array
     */
    private $authors;

    /**
     * @var CollectionFactory
     */
    private $authorCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        AuthorFactory $authorFactory,
        AuthorResource $authorResource,
        CollectionFactory $authorCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->authorFactory = $authorFactory;
        $this->authorResource = $authorResource;
        $this->authorCollectionFactory = $authorCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param AuthorInterface $author
     *
     * @return AuthorInterface
     * @throws CouldNotSaveException
     */
    public function save(AuthorInterface $author)
    {
        try {
            if ($author->getAuthorId()) {
                $author = $this->getById($author->getAuthorId())->addData($author->getData());
            } else {
                $author->setAuthorId(null);
            }
            $this->authorResource->save($author);
            unset($this->authors[$author->getAuthorId()]);
        } catch (\Exception $e) {
            if ($author->getAuthorId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save author with ID %1. Error: %2',
                        [$author->getAuthorId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(__('Unable to save new author. Error: %1', $e->getMessage()));
        }

        return $author;
    }

    /**
     * @param int $authorId
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($authorId)
    {
        if (!isset($this->authors[$authorId])) {
            /** @var \MageSquare\Blog\Model\Author $author */
            $author = $this->authorFactory->create();
            $this->authorResource->load($author, $authorId);
            /**
             * @TODO add NoSuchEntityException for Author management
             */
            $this->authors[$authorId] = $author;
        }

        return $this->authors[$authorId];
    }

    /**
     * @param $urlKey
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function getByName($name)
    {
        $author = $this->authorFactory->create();
        $this->authorResource->load($author, $name, AuthorInterface::NAME);

        return $author;
    }

    /**
     * @param $urlKey
     *
     * @return AuthorInterface
     */
    public function getByUrlKey($urlKey)
    {
        return $this->getByUrlKeyAndStoreId($urlKey);
    }

    public function getByUrlKeyAndStoreId(?string $urlKey, int $storeId = Store::DEFAULT_STORE_ID): AuthorInterface
    {
        $collection = $this->authorCollectionFactory->create();
        $collection->addStoreWithDefault($storeId);
        $collection->addStoreFieldToFilter(AuthorInterface::URL_KEY, $urlKey);
        $collection->setLimit(1);
        /** @var AuthorInterface $authorByUrlKey **/
        $authorByUrlKey = $collection->getFirstItem();

        return $authorByUrlKey;
    }

    /**
     * @param AuthorInterface $author
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(AuthorInterface $author)
    {
        try {
            $this->authorResource->delete($author);
            unset($this->authors[$author->getAuthorId()]);
        } catch (\Exception $e) {
            if ($author->getAuthorId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove author with ID %1. Error: %2',
                        [$author->getAuthorId(), $e->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(__('Unable to remove author. Error: %1', $e->getMessage()));
        }

        return true;
    }

    /**
     * @param int $authorId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($authorId)
    {
        $authorModel = $this->getById($authorId);
        $this->delete($authorModel);

        return true;
    }

    /**
     * @param string[] $authors
     *
     * @return \MageSquare\Blog\Model\ResourceModel\Author\Collection
     */
    public function getList($authors)
    {
        return $this->authorCollectionFactory->create()->addFieldToFilter(AuthorInterface::NAME, ['in' => $authors]);
    }

    /**
     * @return \MageSquare\Blog\Model\Author
     */
    public function getAuthorModel()
    {
        return $this->authorFactory->create();
    }

    /**
     * @return AuthorResource\Collection
     */
    public function getAuthorCollection()
    {
        return $this->authorCollectionFactory->create()->addDefaultStore();
    }

    /**
     * @param string $name
     * @param string $facebookProfile
     * @param string $twitterProfile
     * @return AuthorInterface
     */
    public function createAuthor($name, $facebookProfile, $twitterProfile)
    {
        return $this->authorResource->createAuthor($name, $facebookProfile, $twitterProfile);
    }

    /**
     * @param int $authorId
     * @param int $storeId
     * @param bool $isAddDefaultStore
     * @return \Magento\Framework\DataObject
     */
    public function getByIdAndStore($authorId, $storeId = 0, $isAddDefaultStore = true)
    {
        $collection = $this->authorCollectionFactory->create();
        if ($isAddDefaultStore) {
            $collection->addStoreWithDefault($storeId);
        } else {
            $collection->addStore($storeId);
        }

        $collection->addFieldToFilter(AuthorInterface::AUTHOR_ID, $authorId);
        $collection->setLimit(1);

        return $collection->getFirstItem();
    }
}
