<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content;

class Search extends \MageSquare\Blog\Block\Content\Lists
{
    const SPECIAL_CHARACTERS = '-+~/<>\'":*$#@()!,.?`=%&^';

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getToolbar()
            ->setSearchPage(true)
            ->setQuery(sprintf("query=%s", $this->getRequest()->getParam('query')));

        return $this;
    }

    /**
     * @return AbstractBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        parent::prepareBreadcrumbs();
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $title = __("Search results for '%1'", $this->getQueryText());
            $breadcrumbs->addCrumb(
                'search',
                [
                    'label' => $title,
                    'title' => $title,
                ]
            );
        }
    }

    /**
     * @return \MageSquare\Blog\Model\ResourceModel\Posts\Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $posts = $this->getPostRepository()->getActivePosts()
                ->addSearchFilter($this->getQueryText());
            $this->collection = $posts;
        }

        return $this->collection;
    }

    /**
     * @return string
     */
    private function getQueryText()
    {
        $replaceSymbols = str_split(self::SPECIAL_CHARACTERS);

        return str_replace($replaceSymbols, '', $this->getRequest()->getParam('query'));
    }
}
