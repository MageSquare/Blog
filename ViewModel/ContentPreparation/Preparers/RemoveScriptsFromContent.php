<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\ContentPreparation\Preparers;

class RemoveScriptsFromContent implements PreparerInterface
{
    public function prepare(string $content): string
    {
        return preg_replace(
            '/<script.+?\/script>/is',
            '',
            $content
        );
    }
}
