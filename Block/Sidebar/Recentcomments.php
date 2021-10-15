<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Sidebar;

class Recentcomments extends AbstractClass
{
    /**
     * @var \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private $collection;

    /**
     * @var \MageSquare\Blog\Api\CommentRepositoryInterface
     */
    private $commentRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Date $dateHelper,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Api\CommentRepositoryInterface $commentRepository,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->commentRepository = $commentRepository;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("MageSquare_Blog::sidebar/recentcomments.phtml");
        $this->addAmpTemplate("MageSquare_Blog::amp/sidebar/recentcomments.phtml");
        $this->setRoute('display_recent_comments');
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = '';
        if ($this->getSettingsHelper()->getUseComments()) {
            $html = parent::toHtml();
        }

        return $html;
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getBlockHeader()
    {
        if (!$this->hasData('header_text')) {
            $this->setData('header_text', __('Recent Comments'));
        }

        return $this->getData('header_text');
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Comments\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCommentsCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->commentRepository->getRecentComments();
            $this->collection->setPageSize($this->getCommentsLimit());
        }

        return $this->collection;
    }

    /**
     * Get show thesis
     *
     * @return bool
     */
    public function needShowThesis()
    {
        return (bool)$this->getSettingsHelper()->getRecentCommentsDisplayShort();
    }

    /**
     * Get show date
     *
     * @return bool
     */
    public function needShowDate()
    {
        if (!$this->hasData('display_date')) {
            $this->setData('display_date', $this->getSettingsHelper()->getRecentCommentsDisplayDate());
        }

        return (bool)$this->getData('display_date');
    }

    /**
     * @return string
     */
    public function getCommentsLimit()
    {
        if (!$this->hasData('comments_limit')) {
            $this->setData('comments_limit', $this->getSettingsHelper()->getCommentsLimit());
        }

        return $this->getData('comments_limit');
    }
}
