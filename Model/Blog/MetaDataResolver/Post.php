<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Blog\MetaDataResolver;

use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver;
use Magento\Framework\View\Result\Page as ResultPage;

class Post
{
    /**
     * @var MetaDataResolver
     */
    private $resolver;

    public function __construct(MetaDataResolver $metaDataResolver)
    {
        $this->resolver = $metaDataResolver;
    }

    public function resolve(ResultPage $resultPage, PostInterface $post): void
    {
        $this->resolver->preparePageMetadata(
            $resultPage,
            (string)$post->getMetaTitle(),
            (string)$post->getMetaTags(),
            (string)($post->getMetaDescription() ?: $this->resolver->cutDescription((string)$post->getShortContent())),
            (string)$post->getUrl(),
            (string)$post->getTitle()
        );
    }
}
