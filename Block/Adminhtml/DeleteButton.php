<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Adminhtml;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \MageSquare\Blog\Model\BlogRegistry
     */
    private $blogRegistry;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageSquare\Blog\Model\BlogRegistry $blogRegistry
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->blogRegistry = $blogRegistry;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $id = $this->getItemId();
        if ($id) {
            $deleteUrl = $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . $this->getConfirmText()
                    . '\', \'' . $deleteUrl . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return \MageSquare\Blog\Model\BlogRegistry
     */
    public function getRegistry()
    {
        return $this->blogRegistry;
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getConfirmText()
    {
        return __('Are you sure you want to delete this?');
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return 0;
    }
}
