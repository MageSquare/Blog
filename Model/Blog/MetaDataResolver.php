<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Blog;

use MageSquare\Blog\Model\ConfigProvider;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Framework\View\Result\Page as ResultPage;

class MetaDataResolver
{
    /**
     * @var StringUtils
     */
    private $string;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        StringUtils $string,
        ConfigProvider $configProvider
    ) {
        $this->string = $string;
        $this->configProvider = $configProvider;
    }

    public function preparePageMetadata(
        ResultPage $resultPage,
        string $metaTitle,
        string $keyword,
        string $description,
        string $url,
        string $title
    ): void {
        $pageLayout = $resultPage->getLayout();
        $pageConfig = $resultPage->getConfig();

        $pageConfig->setMetaTitle($this->modifyMetaTitle($metaTitle));
        $pageConfig->getTitle()->set($metaTitle ?: $title);

        if ($keyword) {
            $pageConfig->setKeywords($keyword);
        }

        if ($description) {
            $pageConfig->setDescription($description);
        }

        if ($url) {
            $pageConfig->addRemotePageAsset(
                $url,
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        $pageMainTitle = $pageLayout->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }
    }

    private function modifyMetaTitle(string $metaTitle): string
    {
        $prefix = $this->configProvider->getTitlePrefix();
        if ($prefix) {
            $metaTitle = $prefix . ' - ' . $metaTitle;
        }

        $suffix = $this->configProvider->getTitleSuffix();
        if ($suffix) {
            $metaTitle .= ' | ' . $suffix;
        }

        return $metaTitle;
    }

    public function cutDescription(string $description): string
    {
        return $this->string->substr(strip_tags($description), 0, 255);
    }
}
