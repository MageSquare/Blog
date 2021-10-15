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
use MageSquare\Blog\Model\ConfigProvider;
use MageSquare\Blog\Model\UrlResolver;
use Magento\Framework\View\Result\Page as ResultPage;

class Home
{
    /**
     * @var MetaDataResolver
     */
    private $resolver;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var UrlResolver
     */
    private $urlResolver;

    public function __construct(
        MetaDataResolver $metaDataResolver,
        ConfigProvider $configProvider,
        UrlResolver $urlResolver
    ) {
        $this->resolver = $metaDataResolver;
        $this->configProvider = $configProvider;
        $this->urlResolver = $urlResolver;
    }

    public function resolve(ResultPage $resultPage): void
    {
        $this->resolver->preparePageMetadata(
            $resultPage,
            (string)$this->configProvider->getMetaTitle(),
            (string)$this->configProvider->getMetaTags(),
            (string)$this->configProvider->getMetaDescription(),
            $this->urlResolver->getBlogUrl(),
            (string)$this->configProvider->getTitle()
        );
    }
}
