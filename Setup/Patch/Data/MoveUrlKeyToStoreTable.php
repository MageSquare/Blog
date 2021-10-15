<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Setup\Patch\Data;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\ResourceModel\Author;
use MageSquare\Blog\Model\ResourceModel\Categories;
use MageSquare\Blog\Model\ResourceModel\Tag;
use MageSquare\Blog\Setup\Patch\DeclarativeSchemaApplyBefore\ExtractUrlKeysFromBlogEntities;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Magento\Store\Model\Store;

class MoveUrlKeyToStoreTable implements DataPatchInterface, NonTransactionableInterface
{
    const BATCH_SIZE = 500;

    const ENTITIES_TABLES = [
        CategoryInterface::CATEGORY_ID => Categories::STORE_TABLE_NAME,
        AuthorInterface::AUTHOR_ID => Author::STORE_TABLE_NAME,
        TagInterface::TAG_ID => Tag::STORE_TABLE_NAME
    ];

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $temporaryTableName = $this->moduleDataSetup->getTable(
            ExtractUrlKeysFromBlogEntities::TEMPORARY_TABLE_NAME
        );
        $connection = $this->moduleDataSetup->getConnection();

        if ($connection->isTableExists($temporaryTableName)) {
            try {
                $connection->beginTransaction();

                foreach (self::ENTITIES_TABLES as $entityIdentifier => $tableName) {
                    $tableName = $this->moduleDataSetup->getTable($tableName);

                    foreach ($this->getEntitiesForUpdate($entityIdentifier) as $entityData) {
                        $urlKey = $entityData[ExtractUrlKeysFromBlogEntities::URL_KEY];
                        $connection->update(
                            $tableName,
                            [ExtractUrlKeysFromBlogEntities::URL_KEY => $urlKey],
                            sprintf(
                                '%s = %d AND %s = %d',
                                $entityIdentifier,
                                (int)$entityData['entity_id'],
                                'store_id',
                                Store::DEFAULT_STORE_ID
                            )
                        );
                    }
                }

                $connection->commit();
                $connection->dropTable($temporaryTableName);
            } catch (\Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    private function getEntitiesForUpdate(string $type): iterable
    {
        $select = $this->getConnection()->select();
        $select
            ->from($this->moduleDataSetup->getTable(ExtractUrlKeysFromBlogEntities::TEMPORARY_TABLE_NAME))
            ->where('type = ?', $type);
        $rowsCount = $this->getRowsCount($select);
        $pageAmount = (int) ceil($rowsCount / self::BATCH_SIZE);

        for ($currentPage = 1; $currentPage <= $pageAmount; $currentPage++) {
            $select->limitPage($currentPage, self::BATCH_SIZE);

            yield from $this->getConnection()->fetchAll($select) ?: [];
        }
    }

    private function getConnection(): AdapterInterface
    {
        return $this->moduleDataSetup->getConnection();
    }

    private function getRowsCount(Select $select): int
    {
        $countSelect = clone $select;
        $countSelect->reset(Select::ORDER);
        $countSelect->reset(Select::LIMIT_COUNT);
        $countSelect->reset(Select::LIMIT_OFFSET);
        $countSelect->reset(Select::COLUMNS);
        $countSelect->columns(['count' => new \Zend_Db_Expr('COUNT(*)')]);

        return (int)$this->getConnection()->fetchOne($countSelect);
    }
}
