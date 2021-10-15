<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Blog\MetaDataResolver;

use MageSquare\Blog\Api\Data\CategoryInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver;
use Magento\Framework\View\Result\Page as ResultPage;

class Category
{
    /**
     * @var MetaDataResolver
     */
    private $resolver;

    public function __construct(MetaDataResolver $metaDataResolver)
    {
        $this->resolver = $metaDataResolver;
    }

    public function resolve(ResultPage $resultPage, CategoryInterface $category): void
    {
        $this->resolver->preparePageMetadata(
            $resultPage,
            (string)$category->getMetaTitle(),
            (string)$category->getMetaTags(),
            (string)$category->getMetaDescription(),
            (string)$category->getUrl(),
            (string)$category->getName()
        );
    }
}
