<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Blog\MetaDataResolver;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver;
use Magento\Framework\View\Result\Page as ResultPage;

class Author
{
    /**
     * @var MetaDataResolver
     */
    private $resolver;

    public function __construct(MetaDataResolver $metaDataResolver)
    {
        $this->resolver = $metaDataResolver;
    }

    public function resolve(ResultPage $resultPage, AuthorInterface $author): void
    {
        $this->resolver->preparePageMetadata(
            $resultPage,
            (string)$author->getMetaTitle(),
            (string)$author->getMetaTags(),
            (string)($author->getMetaDescription()
                ?: $this->resolver->cutDescription((string)$author->getShortDescription())),
            (string)$author->getUrl(),
            __('Articles by %1', $author->getName())->render()
        );
    }
}
