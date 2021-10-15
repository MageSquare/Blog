<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\ResourceModel;

use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\ResourceModel\Traits\ResourceModelTrait;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Tag extends AbstractDb
{
    use ResourceModelTrait;

    const TABLE_NAME = 'magesquare_blog_tags';

    const STORE_TABLE_NAME = 'magesquare_blog_tags_store';

    const STORE_TABLE_FIELDS = [
        TagInterface::TAG_ID,
        TagInterface::STORE_ID,
        TagInterface::NAME,
        TagInterface::META_TITLE,
        TagInterface::META_DESCRIPTION,
        TagInterface::META_TAGS,
        TagInterface::URL_KEY,
    ];

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, TagInterface::TAG_ID);
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
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$object->getId() && !$object->hasData(TagInterface::URL_KEY)) {
            $name = str_replace('/', ' ', $object->getData(TagInterface::NAME));
            $slug = $this->generateSlug($name);
            $object->setData(TagInterface::URL_KEY, $slug);
        }

        return parent::_beforeSave($object);
    }

    /**
     * @param $title
     * @return string
     */
    private function generateSlug($title)
    {
        $title = urldecode($title);
        $title = strtolower(
            preg_replace(['/[^\\P{Han}a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'], ['', '-', ''], $title)
        );

        return $title;
    }
}
