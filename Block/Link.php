<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block;

use MageSquare\Blog\Helper\Settings;

class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    const AMBLOG_TOOLBAR_LINK_ULTIMO = 'msblog_toolbar_link_ultimo';

    /**
     * @var Settings
     */
    private $settingsHelper;

    /**
     * @var \MageSquare\Blog\Model\UrlResolver
     */
    private $urlResolver;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->settingsHelper = $settingsHelper;
        $this->urlResolver = $urlResolver;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        if ($this->getNameInLayout() === self::AMBLOG_TOOLBAR_LINK_ULTIMO
            && !$this->moduleManager->isEnabled('Infortis_Ultimo')
        ) {
            return '';
        }

        return parent::toHtml();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->urlResolver->getBlogUrl();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->settingsHelper->getBlogLabel();
    }

    /**
     * @return bool
     */
    public function showInNavMenu()
    {
        return $this->settingsHelper->showInNavMenu();
    }
}
