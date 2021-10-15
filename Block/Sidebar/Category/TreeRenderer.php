<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Sidebar\Category;

use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Model\ResourceModel\Categories\Collection;
use Magento\Store\Model\Store;

class TreeRenderer extends \MageSquare\Blog\Block\Sidebar\AbstractClass
{
    const LEVEL_LIMIT = 3;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var \MageSquare\Blog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var null|array
     */
    private $postsCount;

    /**
     * @var null|int
     */
    private $categoriesLimit;

    /**
     * @var null|int
     */
    private $parentId;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Date $dateHelper,
        \MageSquare\Blog\Helper\Data $dataHelper,
        \MageSquare\Blog\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $settingsHelper, $dateHelper, $dataHelper, $data);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @param int|null $parentId
     */
    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("MageSquare_Blog::sidebar/categories/tree.phtml");
        $this->addAmpTemplate('MageSquare_Blog::amp/sidebar/categories/tree.phtml');
    }

    /**
     * @param int $parentId
     * @return string
     */
    public function render($parentId = CategoryInterface::ROOT_CATEGORY_ID): string
    {
        $this->setParentId((int)$parentId);
        return $this->toHtml();
    }

    /**
     * Render all children for current category path
     *
     * @param int $parentId
     * @return string
     */
    public function renderChildrenItems(int $parentId): string
    {
        return $this->render($parentId);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection()
    {
        return $this->collection = $this->categoryRepository->getChildrenCategories(
            $this->getParentId(),
            $this->getCategoriesLimit()
        );
    }

    /**
     * @param $categoryId
     *
     * @return int
     */
    public function getPostsCount($categoryId)
    {
        if ($this->postsCount === null) {
            $this->postsCount = $this->collection->getPostsCount(
                [
                    $this->_storeManager->getStore()->getId(),
                    Store::DEFAULT_STORE_ID
                ]
            );
        }

        return isset($this->postsCount[$categoryId]) ? $this->postsCount[$categoryId] : 0;
    }

    public function getCategoriesLimit(): ?int
    {
        if (!$this->categoriesLimit) {
            $this->categoriesLimit = $this->getSettingsHelper()->getCategoriesLimit();
        }

        return $this->categoriesLimit;
    }

    /**
     * @param int $categoriesLimit
     *
     * @return $this
     */
    public function setCategoriesLimit(int $categoriesLimit): self
    {
        $this->categoriesLimit = $categoriesLimit;

        return $this;
    }
}
