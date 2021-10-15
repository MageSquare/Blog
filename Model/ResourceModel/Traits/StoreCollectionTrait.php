<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\ResourceModel\Traits;

use InvalidArgumentException;
use Magento\Framework\Data\Collection;
use Zend_Db_Expr;

trait StoreCollectionTrait
{
    /**
     * @param bool $isAddColumns
     * @return $this
     */
    public function addDefaultStore($isAddColumns = true)
    {
        $table = $this->getMainTable() . '_store';
        $idFieldName = $this->getResource()->getIdFieldName();
        $this->getSelect()->joinLeft(
            ['store' => $table],
            sprintf(
                'store.%s = main_table.%s AND store.store_id = %s',
                $idFieldName,
                $idFieldName,
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            ),
            []
        )->group('main_table.' . $idFieldName);
        if ($isAddColumns) {
            $this->getSelect()->columns('store.*');
        }
        if (method_exists($this, 'setIsStoreDataAdded')) {
            $this->setIsStoreDataAdded(true);
        }
        return $this;
    }

    /**
     * @param $storeId
     * @param bool $isAddColumns
     * @return $this
     */
    public function addStore($storeId, $isAddColumns = true)
    {
        $table = $this->getMainTable() . '_store';
        $idFieldName = $this->getResource()->getIdFieldName();

        $this->getSelect()->joinLeft(
            ['noDefaultStore' => $table],
            sprintf(
                'noDefaultStore.%s = main_table.%s AND noDefaultStore.store_id = %s',
                $idFieldName,
                $idFieldName,
                $storeId
            ),
            []
        );
        if ($isAddColumns) {
            $this->getSelect()->columns('noDefaultStore.*');
        }

        return $this;
    }

    /**
     * @param int $storeId
     * @return $this|\MageSquare\Blog\Model\ResourceModel\Abstracts\Collection
     */
    public function addStoreWithDefault($storeId)
    {
        $this->addDefaultStore(false)->addStore($storeId, false);
        $idFieldName = $this->getResource()->getIdFieldName();
        foreach (self::MULTI_STORE_FIELDS_MAP as $key => $field) {
            $this->getSelect()->columns([$key => $field]);
        }
        $this->getSelect()->group('main_table.' . $idFieldName);

        return $this;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->getSelect()->limit($limit);
    }

    /**
     * @param string $column
     * @return string
     */
    public function getStoreColumn($column)
    {
        return self::MULTI_STORE_FIELDS_MAP[$column] ?? $column;
    }

    public function addStoreFieldToFilter($field, $condition = null): void
    {
        if (!isset(self::MULTI_STORE_FIELDS_MAP[$field])) {
            throw new InvalidArgumentException(__("Store configured field `{$field}` doesn't exists")->render());
        }

        $field = new Zend_Db_Expr(self::MULTI_STORE_FIELDS_MAP[$field]);
        $this->getSelect()->where($this->getConnection()->prepareSqlCondition($field, $condition));
    }
}
