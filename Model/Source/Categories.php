<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use MageSquare\Blog\Api\CategoryRepositoryInterface;

class Categories implements OptionSourceInterface
{
    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $categories = [];
        $collection = $this->categoryRepository->getAllCategories();
        foreach ($collection as $category) {
            $categories[] = [
                'value' => $category->getCategoryId(),
                'label' => $category->getName()
            ];
        }

        return $categories;
    }
}
