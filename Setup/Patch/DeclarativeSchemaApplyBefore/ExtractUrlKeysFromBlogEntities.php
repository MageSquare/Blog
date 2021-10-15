<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Setup\Patch\DeclarativeSchemaApplyBefore;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\ResourceModel\Author;
use MageSquare\Blog\Model\ResourceModel\Categories;
use MageSquare\Blog\Model\ResourceModel\Tag;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ExtractUrlKeysFromBlogEntities implements PatchInterface
{
    const ENTITIES_TABLES = [
        CategoryInterface::CATEGORY_ID => Categories::TABLE_NAME,
        AuthorInterface::AUTHOR_ID => Author::TABLE_NAME,
        TagInterface::TAG_ID => Tag::TABLE_NAME
    ];

    const TEMPORARY_TABLE_NAME = 'magesquare_blog_entities_url_keys';
    const URL_KEY = 'url_key';

    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
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
        $connection = $this->getConnection();

        if ($this->isCanApply()) {
            $tmpTableName = $this->createTemporaryTable();
            $connection->beginTransaction();

            try {
                foreach ($this->prepareSelects() as $select) {
                    $connection->query($connection->insertFromSelect($select, $tmpTableName));
                }

                $connection->commit();
            } catch (\Exception $e) {
                $connection->rollBack();
                throw $e;
            }
        }

        return $this;
    }

    private function isCanApply(): bool
    {
        return array_reduce(self::ENTITIES_TABLES, function (bool $carry, string $tableName): bool {
            $tableName = $this->schemaSetup->getTable($tableName);
            $connection = $this->getConnection();

            return $carry && $connection->isTableExists($tableName) && $connection->tableColumnExists(
                $tableName,
                self::URL_KEY
            );
        }, true);
    }

    private function getConnection(): AdapterInterface
    {
        return $this->schemaSetup->getConnection();
    }

    /**
     * @return Select[]
     */
    private function prepareSelects(): array
    {
        $selects = [];

        foreach (self::ENTITIES_TABLES as $tableIdentifier => $tableName) {
            $select = $this->getConnection()->select();
            $selects[] = $select->from(
                $this->schemaSetup->getTable($tableName),
                [
                    self::URL_KEY => self::URL_KEY,
                    'entity_id' => $tableIdentifier,
                    'type' => new \Zend_Db_Expr("'{$tableIdentifier}'")
                ]
            );
        }

        return $selects;
    }

    private function createTemporaryTable(): string
    {
        $this->schemaSetup->startSetup();
        $connection = $this->getConnection();
        $tableName = $this->schemaSetup->getTable(self::TEMPORARY_TABLE_NAME);
        $table = $connection->newTable($tableName);
        $table->addColumn(
            self::URL_KEY,
            Table::TYPE_TEXT,
            null,
            [
                Table::OPTION_NULLABLE => true,
                Table::OPTION_LENGTH => 255
            ]
        );
        $table->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                Table::OPTION_NULLABLE => false
            ]
        );
        $table->addColumn(
            'type',
            Table::TYPE_TEXT,
            null,
            [
                Table::OPTION_NULLABLE => true,
                Table::OPTION_LENGTH => 255
            ]
        );
        $connection->createTable($table);
        $this->schemaSetup->endSetup();

        return $tableName;
    }
}
