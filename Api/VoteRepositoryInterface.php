<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Api;

/**
 * Interface VoteRepositoryInterface
 * @api
 */
interface VoteRepositoryInterface
{
    /**
     * @param \MageSquare\Blog\Api\Data\VoteInterface $vote
     * @return \MageSquare\Blog\Api\Data\VoteInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\MageSquare\Blog\Api\Data\VoteInterface $vote);

    /**
     * @param int $voteId
     * @return \MageSquare\Blog\Api\Data\VoteInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($voteId);

    /**
     * @param Data\VoteInterface $vote
     * @return mixed
     */
    public function delete(\MageSquare\Blog\Api\Data\VoteInterface $vote);

    /**
     * @param int $voteId
     *
     * @return boolean
     */
    public function deleteById($voteId);
}
