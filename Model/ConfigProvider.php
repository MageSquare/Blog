<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model;

use MageSquare\Base\Model\ConfigProviderAbstract;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filter\FilterManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    const SEARCH_ENGINE_ROUTE = 'search_engine/route';
    const REDIRECT_URL_POSTFIX = 'redirect/url_postfix';
    const IS_ASK_EMAIL = 'comments/ask_email';
    const IS_ASK_NAME = 'comments/ask_name';
    const IS_NOTIFY_ABOUT_REPLIES = 'comments/notify_about_replies';
    const NOTIFY_ABOUT_REPLIES_SENDER = 'comments/sender';
    const NOTIFY_ABOUT_REPLIES_TEMPLATE = 'comments/email_template';
    const IS_SHOW_GDPR = 'comments/gdpr';
    const GDPR_TEXT = 'comments/gdpr_text';
    const TITLE_PREFIX = 'search_engine/title_prefix';
    const TITLE_SUFFIX = 'search_engine/title_suffix';
    const TITLE = 'search_engine/title';
    const META_TITLE = 'search_engine/meta_title';
    const META_DESCRIPTION = 'search_engine/meta_description';
    const META_KEYWORDS = 'search_engine/meta_keywords';
    const XML_PATH_POST_PRODUCT_SHOW_ON_PDP = 'posts_products_relation/show_related_posts_on_pp';
    const XML_PATH_POST_PRODUCT_PDP_BLOCK_TITLE = 'posts_products_relation/related_posts_tab_title';
    const XML_PATH_POST_PRODUCT_SHOW_ON_POST_PAGE = 'posts_products_relation/show_rp_on_post_page';
    const XML_PATH_POST_PRODUCT_POST_PAGE_BLOCK_TITLE = 'posts_products_relation/rp_block_title';

    const CATEGORY_LIMIT_ON_POST = 'category/limit_on_post';

    const DISPLAY_AUTHOR = 'post/display_author';
    const ICON_COLOR_CLASS = 'style/color_sheme';

    /**
     * @var string
     */
    protected $pathPrefix = 'msblog/';

    /**
     * @var FilterManager
     */
    private $filterManager;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        FilterManager $filterManager
    ) {
        parent::__construct($scopeConfig);
        $this->filterManager = $filterManager;
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isAskEmail($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_ASK_EMAIL, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isAskName($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_ASK_NAME, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return bool
     */
    public function isShowGdpr($scopeCode = null)
    {
        return (bool)$this->getValue(self::IS_SHOW_GDPR, $scopeCode);
    }

    /**
     * @param string|null $scopeCode
     *
     * @return string
     */
    public function getGdprText($scopeCode = null)
    {
        return $this->filterManager->stripTags(
            $this->getValue(self::GDPR_TEXT, $scopeCode),
            [
                'allowableTags' => '<a>',
                'escape' => false
            ]
        );
    }

    public function getBlogPostfix(?StoreInterface $store = null): string
    {
        return (string)$this->getValue(self::REDIRECT_URL_POSTFIX, $store);
    }

    public function getSeoRoute(?StoreInterface $store = null): string
    {
        return trim((string)$this->getValue(self::SEARCH_ENGINE_ROUTE, $store));
    }

    public function getTitleSuffix(): string
    {
        return (string)$this->getValue(self::TITLE_SUFFIX);
    }

    public function getTitlePrefix(): string
    {
        return (string)$this->getValue(self::TITLE_PREFIX);
    }

    public function getMetaTitle(): string
    {
        return (string)$this->getValue(self::META_TITLE);
    }

    public function getMetaTags(): string
    {
        return (string)$this->getValue(self::META_KEYWORDS);
    }

    public function getMetaDescription(): string
    {
        return (string)$this->getValue(self::META_DESCRIPTION);
    }

    public function getTitle(): string
    {
        return (string) $this->getValue(self::TITLE);
    }

    public function isNotifyAboutReplies(): bool
    {
        return (bool) $this->getValue(self::IS_NOTIFY_ABOUT_REPLIES);
    }

    public function notifyAboutRepliesSender(): string
    {
        return $this->getValue(self::NOTIFY_ABOUT_REPLIES_SENDER);
    }

    public function notifyAboutRepliesTemplate(): string
    {
        return $this->getValue(self::NOTIFY_ABOUT_REPLIES_TEMPLATE);
    }

    public function isShowPostPageBlockOnProductPage(): bool
    {
        return (bool)$this->getValue(self::XML_PATH_POST_PRODUCT_SHOW_ON_PDP);
    }

    public function getPostPageBlockTitleOnProductPage(): string
    {
        return (string)$this->getValue(self::XML_PATH_POST_PRODUCT_PDP_BLOCK_TITLE);
    }

    public function isShowPostPageBlockOnPostPage(): bool
    {
        return (bool)$this->getValue(self::XML_PATH_POST_PRODUCT_SHOW_ON_POST_PAGE);
    }

    public function getPostPageBlockTitleOnPostPage(): string
    {
        return (string)$this->getValue(self::XML_PATH_POST_PRODUCT_POST_PAGE_BLOCK_TITLE);
    }

    public function getCategoryLimitOnPost(): int
    {
        return (int) $this->getValue(self::CATEGORY_LIMIT_ON_POST);
    }

    public function getIconColorClass(): string
    {
        return (string) $this->getValue(self::ICON_COLOR_CLASS);
    }

    public function isShowAuthorInfo(): bool
    {
        return $this->isSetFlag(self::DISPLAY_AUTHOR);
    }
}
