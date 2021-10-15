<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Setup;

use MageSquare\Blog\Api\Data\VoteInterface;
use MageSquare\Blog\Model\ResourceModel\Author;
use MageSquare\Blog\Model\ResourceModel\Categories;
use MageSquare\Blog\Model\ResourceModel\Categories\Collection;
use MageSquare\Blog\Model\ResourceModel\Comments;
use MageSquare\Blog\Model\ResourceModel\Posts;
use MageSquare\Blog\Model\ResourceModel\Posts\RelatedProducts\GetPostRelatedProducts;
use MageSquare\Blog\Model\ResourceModel\Tag;
use MageSquare\Blog\Model\ResourceModel\View;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

class Uninstall implements UninstallInterface
{
    const MODULE_TABLES = [
        Author::STORE_TABLE_NAME,
        Author::TABLE_NAME,
        Collection::CATEGORY_POST_RELATION_TABLE,
        Categories::STORE_TABLE_FIELDS,
        Categories::TABLE_NAME,
        Comments::TABLE_NAME,
        Posts::POSTS_STORE_TABLE,
        Posts::POSTS_TAGS_RELATION_TABLE,
        Posts::TABLE_NAME,
        Tag::TABLE_NAME,
        Tag::STORE_TABLE_NAME,
        View::TABLE_NAME,
        VoteInterface::MAIN_TABLE,
        GetPostRelatedProducts::POST_PRODUCT_RELATION_TABLE
    ];

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $connection = $setup->getConnection();

        foreach (self::MODULE_TABLES as $table) {
            $connection->dropTable($setup->getTable($table));
        }

        $setup->endSetup();
    }
}
