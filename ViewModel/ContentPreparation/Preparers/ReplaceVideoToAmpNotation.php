<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\ContentPreparation\Preparers;

class ReplaceVideoToAmpNotation implements PreparerInterface
{
    public function prepare(string $content): string
    {
        return preg_replace(
            '/<video(.+?)\>(.+?)<\/video>/is',
            '<amp-video $1 layout="responsive">$2</amp-video>',
            $content
        );
    }
}
