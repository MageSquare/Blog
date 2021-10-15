<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Plugin\Block;

use Magento\Framework\Data\Tree\NodeFactory;
use MageSquare\Blog\Block\Link;
use MageSquare\Blog\Model\UrlResolver;

class Topmenu
{
    /**
     * @var NodeFactory
     */
    private $nodeFactory;

    /**
     * @var Link
     */
    protected $link;

    /**
     * @var UrlResolver
     */
    protected $urlResolver;

    public function __construct(
        NodeFactory $nodeFactory,
        Link $link,
        UrlResolver $urlResolver
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->link = $link;
        $this->urlResolver = $urlResolver;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        if ($this->link->showInNavMenu()) {
            $node = $this->nodeFactory->create(
                [
                    'data' => $this->getNodeAsArray(),
                    'idField' => 'id',
                    'tree' => $subject->getMenu()->getTree()
                ]
            );
            $subject->getMenu()->addChild($node);
        }
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getNodeAsArray()
    {
        return [
            'name' => $this->link->getLabel(),
            'id' => 'navmenu_blog',
            'url' => $this->urlResolver->getBlogUrl(),
            'has_active' => false,
            'is_active' => false
        ];
    }
}
