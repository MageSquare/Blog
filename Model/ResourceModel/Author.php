<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\ResourceModel;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Model\ResourceModel\Traits\ResourceModelTrait;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Author extends AbstractDb
{
    use ResourceModelTrait;

    const TABLE_NAME = 'magesquare_blog_author';

    const STORE_TABLE_NAME = 'magesquare_blog_author_store';

    const STORE_TABLE_FIELDS = [
        AuthorInterface::AUTHOR_ID,
        AuthorInterface::STORE_ID,
        AuthorInterface::NAME,
        AuthorInterface::META_TITLE,
        AuthorInterface::META_DESCRIPTION,
        AuthorInterface::META_TAGS,
        AuthorInterface::JOB_TITLE,
        AuthorInterface::SHORT_DESCRIPTION,
        AuthorInterface::DESCRIPTION,
        AuthorInterface::URL_KEY,
    ];

    /**
     * @var \MageSquare\Blog\Model\AuthorFactory
     */
    private $authorFactory;

    /**
     * @var Author\CollectionFactory
     */
    private $authorCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \MageSquare\Blog\Model\AuthorFactory $authorFactory,
        \MageSquare\Blog\Model\ResourceModel\Author\CollectionFactory $authorCollectionFactory,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->authorFactory = $authorFactory;
        $this->authorCollectionFactory = $authorCollectionFactory;
    }

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, AuthorInterface::AUTHOR_ID);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStoreData($object);

        return $this;
    }

    /**
     * @param AbstractModel $object
     * @return $this|AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $this->addDefaultStoreSelect($object);

        return $this;
    }

    /**
     * @param $name
     * @param null $facebookProfile
     * @param null $twitterProfile
     * @return \MageSquare\Blog\Api\Data\AuthorInterface
     */
    public function createAuthor($name, $facebookProfile = null, $twitterProfile = null)
    {
        $author = $this->authorFactory->create();
        $author->setName($name)
            ->setFacebookProfile($facebookProfile)
            ->setTwitterProfile($twitterProfile);
        $author->prepapreUrlKey();
        $this->save($author);
        return $author;
    }

    /**
     * @param string $urlKey
     * @return string
     */
    public function getUniqUrlKey($urlKey)
    {
        $collection = $this->authorCollectionFactory->create();
        $collection->getSelect()->where('url_key like "?%"', $urlKey);
        $collection->getSelect()->order('url_key');
        if ($collection->count()) {
            foreach ($collection as $item) {
                if ($item->getUrlKey() == $urlKey) {
                    $urlKey = preg_match('/(.*)-(\d+)$/', $urlKey, $matches)
                        ? $matches[1] . '-' . ($matches[2] + 1)
                        : $urlKey . '-1';
                }
            }
        }
        return $urlKey;
    }
}
