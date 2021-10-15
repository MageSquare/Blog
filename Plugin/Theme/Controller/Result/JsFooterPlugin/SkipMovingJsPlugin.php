<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Plugin\Theme\Controller\Result\JsFooterPlugin;

use MageSquare\Blog\Helper\Data;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Theme\Controller\Result\JsFooterPlugin as MagentoJsFooterPlugin;

class SkipMovingJsPlugin
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Prevent move js to page bottom in case of AMP page layout specifications for magento 2.4.0 version and lower
     *
     * @param MagentoJsFooterPlugin $subject
     * @param \Closure $proceed
     * @param Http $response
     * @return void
     * @see JsFooterPlugin::beforeSendResponse
     */
    public function aroundBeforeSendResponse(
        MagentoJsFooterPlugin $subject,
        \Closure $proceed,
        Http $response
    ): void {
        if (!$this->helper->isCurrentPageAmp()) {
            $proceed($response);
        }
    }

    /**
     * Prevent move js to page bottom in case of AMP page layout specifications for magento 2.4.1 version and greater
     *
     * @param MagentoJsFooterPlugin $subject
     * @param \Closure $proceed
     * @param Layout $layoutSubject
     * @param Layout $result
     * @param ResponseInterface $httpResponse
     * @return Layout
     * @see JsFooterPlugin::afterRenderResult
     */
    public function aroundAfterRenderResult(
        MagentoJsFooterPlugin $subject,
        \Closure $proceed,
        Layout $layoutSubject,
        Layout $result,
        ResponseInterface $httpResponse
    ): Layout {
        if ($this->helper->isCurrentPageAmp()) {
            return $result;
        }

        return $proceed($layoutSubject, $result, $httpResponse);
    }
}
