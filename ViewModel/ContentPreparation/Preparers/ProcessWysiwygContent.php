<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\ContentPreparation\Preparers;

use Magento\Catalog\Helper\Data;

class ProcessWysiwygContent implements PreparerInterface
{
    /**
     * @var Data
     */
    private $magentoCatalogHelper;

    public function __construct(
        Data $magentoCatalogHelper
    ) {
        $this->magentoCatalogHelper = $magentoCatalogHelper;
    }

    public function prepare(string $content): string
    {
        return $this->magentoCatalogHelper->getPageTemplateProcessor()->filter($content);
    }
}
