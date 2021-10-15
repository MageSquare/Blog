<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\ResourceModel\Vote;

use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Api\Data\VoteInterface;
use MageSquare\Blog\Model\Posts;
use MageSquare\Blog\Model\ResourceModel\Vote as ResourceVote;
use MageSquare\Blog\Model\Source\PostStatus;
use MageSquare\Blog\Model\Vote;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const POST_TABLE_ALIAS = 'post';

    const POSTS_STORE_TABLE_ALIAS = 'posts_store';

    public function _construct()
    {
        $this->_init(
            Vote::class,
            ResourceVote::class
        );
    }

    public function addIpFilter(string $ip): self
    {
        return $this->addFieldToFilter(VoteInterface::IP, $ip);
    }

    public function addStoreFilter(array $storeIds): self
    {
        return $this
            ->joinPostsStoreTable([PostInterface::STORE_ID])
            ->addFieldToFilter(PostInterface::STORE_ID, $storeIds);
    }

    public function sortByPost(string $direction = self::SORT_ORDER_DESC): self
    {
        return $this
            ->joinPostTable([PostInterface::PUBLISHED_AT])
            ->setOrder(PostInterface::PUBLISHED_AT, $direction);
    }

    public function joinPostTable(array $cols = ['*']): self
    {
        if (!isset($this->_joinedTables[self::POST_TABLE_ALIAS])) {
            $this->getSelect()->join(
                [self::POST_TABLE_ALIAS => $this->getTable(Posts::PERSISTENT_NAME)],
                sprintf(
                    'main_table.%s = %s.%s AND %s.%s = %s',
                    PostInterface::POST_ID,
                    self::POST_TABLE_ALIAS,
                    PostInterface::POST_ID,
                    self::POST_TABLE_ALIAS,
                    PostInterface::STATUS,
                    PostStatus::STATUS_ENABLED
                ),
                $cols
            );
            $this->_joinedTables[self::POST_TABLE_ALIAS] = true;
        }

        return $this;
    }

    public function joinPostsStoreTable(array $cols = ['*']): self
    {
        if (!isset($this->_joinedTables[Posts::POSTS_STORE_TABLE])) {
            $this->getSelect()->join(
                [self::POSTS_STORE_TABLE_ALIAS => $this->getTable(Posts::POSTS_STORE_TABLE)],
                sprintf(
                    'main_table.%s = %s.%s',
                    PostInterface::POST_ID,
                    self::POSTS_STORE_TABLE_ALIAS,
                    PostInterface::POST_ID
                ),
                $cols
            );
            $this->_joinedTables[Posts::POSTS_STORE_TABLE] = true;
        }

        return $this;
    }
}
