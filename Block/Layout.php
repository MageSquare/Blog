<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block;

use Magento\Framework\View\Element\Template;

class Layout extends Template
{
    const CONFIG_XML_PATH = 'layout';

    const ROUTE_LIST = 'list';

    const ROUTE_POST = 'post';

    const CACHE_DATA_PREFIX = 'ms_blog_';

    /**
     * @var array
     */
    private $askedBlockIds = [];

    /**
     * @var array
     */
    private $desktop = [];

    /**
     * @var array
     */
    private $mobile = [];

    /**
     * @var \MageSquare\Blog\Helper\Settings
     */
    private $settingsHelper;

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate("MageSquare_Blog::layout.phtml");
    }

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->settingsHelper = $settingsHelper;
    }

    /**
     * @return string
     */
    private function getBlogRoute()
    {
        return in_array($this->getRequest()->getActionName(), ['post', 'preview'])
            ? self::ROUTE_POST : self::ROUTE_LIST;
    }

    /**
     * @param $zone
     * @param $target
     * @return $this
     * @throws \Zend_Json_Exception
     */
    private function loadPerZoneLayoutConfig($zone, &$target)
    {
        $key = sprintf("%s_%s", $zone, $this->getBlogRoute());

        $config = [];
        switch ($key) {
            case 'mobile_list':
                $config = $this->settingsHelper->getMobileList();
                break;
            case 'mobile_post':
                $config = $this->settingsHelper->getMobilePost();
                break;
            case 'desktop_list':
                $config = $this->settingsHelper->getDesktopList();
                break;
            case 'desktop_post':
                $config = $this->settingsHelper->getDesktopPost();
                break;
        }

        $target = \Zend_Json::decode($config);

        return $this;
    }

    /**
     * @throws \Zend_Json_Exception
     */
    private function prepareLayoutConfig()
    {
        $this->loadPerZoneLayoutConfig('mobile', $this->mobile)
            ->loadPerZoneLayoutConfig('desktop', $this->desktop);
    }

    /**
     * @return $this|Template
     * @throws \Zend_Json_Exception
     */
    protected function _prepareLayout()
    {
        $this->prepareLayoutConfig();
        parent::_prepareLayout();

        return $this;
    }

    /**
     * @param $target
     * @param $alias
     * @return bool
     */
    private function isBlockUsedIn($target, $alias)
    {
        $where = ['left_side', 'right_side', 'content'];

        foreach ($where as $listKey) {
            if (isset($target[$listKey]) && is_array($target[$listKey]) && in_array($alias, $target[$listKey])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $alias
     * @return bool
     */
    public function isBlockUsed($alias)
    {
        return $this->isBlockUsedIn($this->mobile, $alias) || $this->isBlockUsedIn($this->desktop, $alias);
    }

    /**
     * @param $alias
     * @return bool|string
     */
    public function getContentBlockHtml($alias)
    {
        return $this->getBlockHtmlByAlias($alias, 'content');
    }

    /**
     * @param $alias
     * @return bool|string
     */
    public function getSidebarBlockHtml($alias)
    {
        return $this->getBlockHtmlByAlias($alias, 'sidebar');
    }

    /**
     * @param $alias
     * @param $type
     * @return bool|string
     */
    private function getBlockHtmlByAlias($alias, $type)
    {
        $result = false;
        $block = $this->getChildBlock('layout_' . $type);
        $id = 'msblog_' . $type . '_' . str_replace("-", "_", $alias);
        if ($block && !$this->isAskedBefore($id)) {
            $html = $block->getChildHtml($alias);
            if (trim($html)) {
                $this->askBlock($id);
                $result = '<div id="' . $id . '">' . $html . '</div>';
            }
        } else {
            $result = '<!-- placeholder -->'; // code for working with js layout
        }

        return $result;
    }

    /**
     * @return bool|mixed
     */
    public function getDesktopLayoutCode()
    {
        return isset($this->desktop['layout']) ? $this->desktop['layout'] : false;
    }

    /**
     * @return bool|mixed
     */
    public function getMobileLayoutCode()
    {
        return isset($this->mobile['layout']) ? $this->mobile['layout'] : false;
    }

    /**
     * @return bool
     */
    public function hasDesktopLeftColumn()
    {
        return in_array($this->getDesktopLayoutCode(), ['two-columns-left', 'three-columns']);
    }

    /**
     * @return bool
     */
    public function hasDesktopRightColumn()
    {
        return in_array($this->getDesktopLayoutCode(), ['two-columns-right', 'three-columns']);
    }

    /**
     * @return bool
     */
    public function hasMobileLeftColumn()
    {
        return in_array($this->getMobileLayoutCode(), ['two-columns-left', 'three-columns']);
    }

    /**
     * @return bool
     */
    public function hasMobileRightColumn()
    {
        return in_array($this->getMobileLayoutCode(), ['two-columns-right', 'three-columns']);
    }

    /**
     * @param $column
     * @return array|mixed
     */
    public function getDesktopBlocks($column)
    {
        $result = [];
        if (isset($this->desktop[$column]) && $this->desktop[$column]) {
            $result = $this->desktop[$column];
        }

        return $result;
    }

    /**
     * @param $column
     * @return array|mixed
     */
    public function getMobileBlocks($column)
    {
        $result = [];
        if (isset($this->mobile[$column]) && $this->mobile[$column]) {
            $result = $this->mobile[$column];
        }

        return $result;
    }

    /**
     * @param $id
     * @return $this
     */
    public function askBlock($id)
    {
        if (!in_array($id, $this->askedBlockIds)) {
            $this->askedBlockIds[] = $id;
        }

        return $this;
    }

    /**
     * @param $id
     * @return bool
     */
    public function isAskedBefore($id)
    {
        return in_array($id, $this->askedBlockIds);
    }

    /**
     * @return string
     */
    public function getAskedBlockSelector()
    {
        $selectors = [];
        foreach ($this->askedBlockIds as $id) {
            $selectors[] = "#" . $id;
        }

        return implode(", ", $selectors);
    }
}
