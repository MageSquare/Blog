<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Blog\MetaDataResolver;

use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\Blog\MetaDataResolver;
use Magento\Framework\View\Result\Page as ResultPage;

class Tag
{
    /**
     * @var MetaDataResolver
     */
    private $resolver;

    public function __construct(MetaDataResolver $metaDataResolver)
    {
        $this->resolver = $metaDataResolver;
    }

    public function resolve(ResultPage $resultPage, TagInterface $tag): void
    {
        $this->resolver->preparePageMetadata(
            $resultPage,
            (string)$tag->getMetaTitle(),
            (string)$tag->getMetaTags(),
            (string)$tag->getMetaDescription(),
            (string)$tag->getUrl(),
            __("Posts tagged '%1'", $tag->getName())->render()
        );
    }
}
