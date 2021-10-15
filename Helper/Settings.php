<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Data\CollectionDataSourceInterface;
use Magento\Store\Model\ScopeInterface;

class Settings extends AbstractHelper implements CollectionDataSourceInterface
{
    /**
     * @param $path
     * @param int $storeId
     * @return mixed
     */
    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            'msblog/' . $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return bool
     */
    public function isDisplayAtFooter()
    {
        return $this->getStoreConfig('msblog/display_settings/display_at_footer');
    }

    /**
     * @return bool
     */
    public function isDisplayAtToolbar()
    {
        return $this->getStoreConfig('msblog/display_settings/display_at_toolbar');
    }

    /**
     * @return bool
     */
    public function isDisplayAtCategoryMenu()
    {
        return $this->getStoreConfig('msblog/display_settings/display_at_category');
    }

    /**
     * @return string
     */
    public function getBlogPostfix()
    {
        return (string) $this->getStoreConfig('msblog/redirect/url_postfix');
    }

    /**
     * @return string
     */
    public function getSeoRoute()
    {
        return trim($this->getStoreConfig('msblog/search_engine/route'));
    }

    /**
     * @return string
     */
    public function getSeoTitle()
    {
        return $this->getStoreConfig('msblog/search_engine/title');
    }

    /**
     * @return string
     */
    public function getBlogLabel()
    {
        return $this->getStoreConfig('msblog/display_settings/label');
    }

    /**
     * @return bool
     */
    public function showInNavMenu()
    {
        return (bool)$this->getStoreConfig('msblog/display_settings/display_at_category');
    }

    /**
     * @return int
     */
    public function getPostsLimit()
    {
        return (int) $this->getStoreConfig('msblog/list/count_per_page');
    }

    /**
     * @return bool
     */
    public function getRedirectToSeoFormattedUrl()
    {
        return $this->getFlag('msblog/redirect/redirect_to_seo_formatted_url');
    }

    /**
     * @return string
     */
    public function getIconColorClass()
    {
        return $this->getStoreConfig('msblog/style/color_sheme');
    }

    /**
     * @return string
     */
    public function getBlogMetaDescription()
    {
        return $this->getStoreConfig('msblog/search_engine/meta_description');
    }

    /**
     * @return string
     */
    public function getBlogMetaTitle()
    {
        return $this->getStoreConfig('msblog/search_engine/meta_title');
    }

    /**
     * @return string
     */
    public function getBlogMetaKeywords()
    {
        return $this->getStoreConfig('msblog/search_engine/meta_keywords');
    }

    /**
     * @return array
     */
    public function getMobileList()
    {
        return $this->getStoreConfig('msblog/layout/mobile_list');
    }

    /**
     * @return string
     */
    public function getMobilePost()
    {
        return $this->getStoreConfig('msblog/layout/mobile_post');
    }

    /**
     * @return string
     */
    public function getDesktopPost()
    {
        return $this->getStoreConfig('msblog/layout/desktop_post');
    }

    /**
     * @return mixed
     */
    public function getDesktopList()
    {
        return $this->getStoreConfig('msblog/layout/desktop_list');
    }

    /**
     * @return bool
     */
    public function getShowAuthor()
    {
        return $this->getStoreConfig('msblog/post/display_author');
    }

    /**
     * @return bool
     */
    public function getDisplayViews()
    {
        return (bool) $this->getStoreConfig('msblog/post/display_views');
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return $this->getStoreConfig('msblog/post/date_manner');
    }

    /**
     * @return bool
     */
    public function getUseTags()
    {
        return $this->getFlag('msblog/post/display_tags');
    }

    /**
     * @return bool
     */
    public function getUseCategories()
    {
        return $this->getFlag('msblog/category/display_categories');
    }

    /**
     * @return int
     */
    public function getCategoriesLimit()
    {
        return (int) $this->getStoreConfig('msblog/category/categories_limit');
    }

    /**
     * @return int
     */
    public function getRecentPostsLimit()
    {
        return $this->getStoreConfig('msblog/recent_posts/record_limit');
    }

    /**
     * @return int
     */
    public function getRecentPostsImageWidth()
    {
        return $this->getStoreConfig('msblog/recent_posts/image_width') ?: 60;
    }

    /**
     * @return int
     */
    public function getRecentPostsImageHeight()
    {
        return $this->getStoreConfig('msblog/recent_posts/image_height') ?: 60;
    }

    /**
     * @return bool
     */
    public function isRecentPostsUseImage()
    {
        return (bool)$this->getStoreConfig('msblog/recent_posts/display_image');
    }

    /**
     * @return bool
     */
    public function getRecentPostsDisplayShort()
    {
        return $this->getStoreConfig('msblog/recent_posts/display_short');
    }

    /**
     * @return bool
     */
    public function getRecentPostsDisplayDate()
    {
        return $this->getStoreConfig('msblog/recent_posts/display_date');
    }

    /**
     * @return int
     */
    public function getCommentsLimit()
    {
        return $this->getStoreConfig('msblog/comments/record_limit');
    }

    /**
     * @return bool
     */
    public function getRecentCommentsDisplayShort()
    {
        return $this->getStoreConfig('msblog/comments/display_short');
    }

    /**
     * @return bool
     */
    public function getRecentCommentsDisplayDate()
    {
        return $this->getStoreConfig('msblog/comments/display_date');
    }

    /**
     * @return int
     */
    public function getTagsMinimalPostCount()
    {
        return $this->getStoreConfig('msblog/tags/minimal_post_count') ?: 0;
    }

    /**
     * @return bool
     */
    public function getTagsMtEnabled()
    {
        return $this->getStoreConfig('msblog/tags/mt_enabled');
    }

    /**
     * @return int
     */
    public function getTagsMtHeight()
    {
        return $this->getStoreConfig('msblog/tags/mt_height');
    }

    /**
     * @return string
     */
    public function getTagsMtTextcolor()
    {
        return $this->getStoreConfig('msblog/tags/mt_textcolor');
    }

    /**
     * @return string
     */
    public function getTagsMtTextcolor2()
    {
        return $this->getStoreConfig('msblog/tags/mt_textcolor2');
    }

    /**
     * @return string
     */
    public function getTagsMtHiColor()
    {
        return $this->getStoreConfig('msblog/tags/mt_hicolor');
    }

    /**
     * @return int
     */
    public function getRecentPostsShortLimit()
    {
        return $this->getStoreConfig('msblog/recent_posts/short_limit');
    }

    /**
     * @return bool
     */
    public function getShowPrintLink()
    {
        return $this->getStoreConfig('msblog/post/display_print');
    }

    /**
     * @param $title
     *
     * @return string
     */
    public function getPrefixTitle($title)
    {
        if ($prefix = $this->getStoreConfig('msblog/search_engine/title')) {
            $title = $prefix . " - " . $title;
        }

        return $title;
    }

    /**
     * @return bool
     */
    public function getSocialEnabled()
    {
        return $this->getStoreConfig('msblog/social/enabled');
    }

    /**
     * @return bool
     */
    public function getHelpfulEnabled()
    {
        return $this->getStoreConfig('msblog/post/helpful');
    }

    /**
     * @return bool
     */
    public function getCommentsAutoapprove()
    {
        return $this->getStoreConfig('msblog/comments/autoapprove');
    }

    /**
     * @param null $route
     *
     * @return mixed
     */
    public function getConfPlace($route = null)
    {
        return $this->getStoreConfig('msblog/general/' . $route);
    }

    /**
     * @return bool
     */
    public function getCommentsAllowGuests()
    {
        return $this->getStoreConfig('msblog/comments/allow_guests');
    }

    /**
     * @return bool
     */
    public function getUseComments()
    {
        return $this->getStoreConfig('msblog/comments/use_comments');
    }

    /**
     * @return string
     */
    public function getBreadcrumb()
    {
        return $this->getStoreConfig('msblog/search_engine/bread') ?: __('Blog');
    }

    /**
     * @return int
     */
    public function getImageWidth()
    {
        return (int)$this->getStoreConfig('msblog/post/image_width');
    }

    /**
     * @return int
     */
    public function getImageHeight()
    {
        return (int)$this->getStoreConfig('msblog/post/image_height');
    }

    /**
     * @return int
     */
    public function getLogoWidth()
    {
        return (int)$this->getStoreConfig('msblog/accelerated_mobile_pages/logo/image_width');
    }

    /**
     * @return int
     */
    public function getLogoHeight()
    {
        return (int)$this->getStoreConfig('msblog/accelerated_mobile_pages/logo/image_height');
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    private function getStoreConfig($key)
    {
        return $this->scopeConfig->getValue($key, ScopeInterface::SCOPE_STORES);
    }

    /**
     * @param $key
     *
     * @return bool
     */
    private function getFlag($key)
    {
        $configValue = $this->scopeConfig->isSetFlag(
            $key,
            ScopeInterface::SCOPE_STORES
        );

        return $configValue;
    }

    /**
     * @return string
     */
    public function getTagColor()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/tag_color');
    }

    /**
     * @return string
     */
    public function getLinkColor()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/link_color');
    }

    /**
     * @return string
     */
    public function getLinkColorHover()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/link_color_hover');
    }

    /**
     * @return string
     */
    public function getButtonBackground()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/button_background_color');
    }

    /**
     * @return string
     */
    public function getButtonBackgroundHover()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/button_background_color_hover');
    }

    /**
     * @return string
     */
    public function getButtonTextColor()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/button_text_color');
    }

    /**
     * @return string
     */
    public function getButtonTextColorHover()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/button_text_color_hover');
    }

    /**
     * @return string
     */
    public function getFooterBackground()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/footer_background');
    }

    /**
     * @return string
     */
    public function getFooterLinkColor()
    {
        return $this->getStoreConfig('msblog/accelerated_mobile_pages/design/footer_link');
    }

    /**
     * @return bool
     */
    public function getCommentNotificationsEnabled()
    {
        return (bool)$this->getModuleConfig('notify_admin_new_comment/enabled');
    }

    /**
     * @return string
     */
    public function getNotificationEmailTemplate()
    {
        return $this->getModuleConfig('notify_admin_new_comment/email_template');
    }

    /**
     * @return string
     */
    public function getNotificationSender()
    {
        return $this->getModuleConfig('notify_admin_new_comment/sender');
    }

    /**
     * @return array[]|false|string[]
     */
    public function getNotificationRecievers()
    {
        return preg_split('/\n|\r\n?/', $this->getModuleConfig('notify_admin_new_comment/receiver'));
    }
}
