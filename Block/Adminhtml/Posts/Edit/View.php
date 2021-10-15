<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml\Posts\Edit;

use MageSquare\Blog\Api\VoteRepositoryInterface;

/**
 * Class View
 */
class View extends \Magento\Backend\Block\Template
{
    /**
     * @var \MageSquare\Blog\Api\ViewRepositoryInterface
     */
    private $viewRepository;

    /**
     * @var string
     */
    protected $_template = 'MageSquare_Blog::posts/edit/view.phtml';

    /**
     * @var null|array
     */
    private $votes = null;

    /**
     * @var VoteRepositoryInterface
     */
    private $voteRepository;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \MageSquare\Blog\Api\ViewRepositoryInterface $viewRepository,
        VoteRepositoryInterface $voteRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewRepository = $viewRepository;
        $this->voteRepository = $voteRepository;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->viewRepository->getViewCountByPostId($this->getRequest()->getParam('id'));
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->getVotes('plus');
    }

    /**
     * @return int
     */
    public function getDisLikes()
    {
        return $this->getVotes('minus');
    }

    /**
     * @param string $type
     * @return int
     */
    private function getVotes($type)
    {
        if ($this->votes === null) {
            $this->votes = $this->voteRepository->getVotesCount($this->getRequest()->getParam('id'));
        }

        return $this->votes[$type];
    }
}
