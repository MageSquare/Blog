<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Controller\AbstractController;

use MageSquare\Blog\Model\Blog\MetaDataResolver\Search as MetaDataResolver;
use MageSquare\Blog\Model\Blog\Registry;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Search extends Action
{
    const SPECIAL_CHARACTERS = '-+~/<>\'":*$#@()!,.?`=%&^';

    const SEARCH_PARAM = 'query';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MetaDataResolver
     */
    private $metaDataResolver;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        MetaDataResolver $metaDataResolver,
        Registry $registry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->metaDataResolver = $metaDataResolver;
        $this->registry = $registry;
    }

    public function execute()
    {
        $page = $this->resultPageFactory->create();
        $this->metaDataResolver->resolve($page, $this->getSearchText());
        $this->registry->register(Registry::SEARCH_PAGE, true, true);

        return $page;
    }

    private function getSearchText(): string
    {
        $replaceSymbols = str_split(self::SPECIAL_CHARACTERS);

        return str_replace($replaceSymbols, '', $this->getRequest()->getParam(self::SEARCH_PARAM, ''));
    }
}
