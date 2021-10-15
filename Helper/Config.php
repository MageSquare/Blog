<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Helper;

/**
 * Class
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_REGISTRY = 'msblog_config';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Config\Data
     */
    private $blogConfig;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Config\Data $blogConfig,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->blogConfig = $blogConfig;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!$this->registry->registry(self::CONFIG_REGISTRY)) {
            $this->registry->register(self::CONFIG_REGISTRY, $this->blogConfig->get());
        }

        return $this->registry->registry(self::CONFIG_REGISTRY);
    }

    /**
     * @param $path
     * @return array
     */
    public function getArrayFromPath($path)
    {
        $config = $this->getConfig();

        return isset($config[$path]) ? $config[$path] : [];
    }
}
