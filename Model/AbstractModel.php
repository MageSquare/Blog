<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model;

use MageSquare\Blog\Helper\Settings;
use Magento\Store\Api\Data\StoreInterface;

class AbstractModel extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManagerInterface;

    /**
     * @var \MageSquare\Blog\Helper\Url
     */
    private $urlHelper;

    /**
     * @var null
     */
    private $storeId = null;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageSquare\Blog\Helper\Url $urlHelper,
        \MageSquare\Blog\Helper\Settings $settings,
        ConfigProvider $configProvider,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->storeManagerInterface = $storeManagerInterface;
        $this->urlHelper = $urlHelper;
        $this->settings = $settings;
        $this->configProvider = $configProvider;
    }

    /**
     * @param $storeId
     *
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        $this->setData('store_id', $storeId);

        return $this;
    }

    /**
     * @param int $page
     * @param StoreInterface|null $store
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($page = 1, ?StoreInterface $store = null)
    {
        $store = $store ?: $this->storeManagerInterface->getStore($this->getCurrentStoreId());
        $baseUrl = $store->getBaseUrl();
        $url = $baseUrl . $this->configProvider->getSeoRoute($store);
        $postfix = $this->configProvider->getBlogPostfix($store);
        $postfix =  $page > 1 ? "{$postfix}?p={$page}" : $postfix;
        $route = $this->getRoute() ? '/' . $this->getRoute() . '/' : '';
        $urlKey = $this->getUrlKey();
        $urlKey = $urlKey && !$this->getRoute() ? '/' . $urlKey : $urlKey;

        return $url . $route . $urlKey . $postfix;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return  '';
    }

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->hasData('store_id') ? $this->getData('store_id') : $this->storeId;
    }

    /**
     * @return Settings
     */
    public function getSettingsHelper()
    {
        return $this->settings;
    }

    /**
     * @return \MageSquare\Blog\Helper\Url
     */
    public function getUrlHelper()
    {
        return $this->urlHelper;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentStoreId()
    {
        return $this->storeManagerInterface->getStore()->getId();
    }
}
