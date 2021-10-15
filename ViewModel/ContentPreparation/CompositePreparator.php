<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\ContentPreparation;

use MageSquare\Blog\ViewModel\ContentPreparation\Preparers\PreparerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CompositePreparator implements ArgumentInterface, PreparerInterface
{
    /**
     * @var PreparerInterface[]
     */
    private $contentPreparers;

    public function __construct(
        array $contentPreparers = []
    ) {
        $this->contentPreparers = $contentPreparers;
    }

    public function prepare(string $content): string
    {
        foreach ($this->contentPreparers as $preparator) {
            if ($preparator instanceof PreparerInterface) {
                $content = $preparator->prepare($content);
            }
        }

        return $content;
    }
}
