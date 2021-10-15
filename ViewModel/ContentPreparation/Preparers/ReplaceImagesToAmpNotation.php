<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\ContentPreparation\Preparers;

class ReplaceImagesToAmpNotation implements PreparerInterface
{
    public function prepare(string $content): string
    {
        return preg_replace(
            '/<img(.+?)\/?>/is',
            '<div class="amp-img-container"><amp-img $1 layout="fill"></amp-img></div>',
            $content
        );
    }
}
