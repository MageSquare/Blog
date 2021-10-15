<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\ResourceModel\Posts\Save;

use MageSquare\Blog\Model\Posts;

interface SavePartInterface
{
    public function execute(Posts $model): void;
}
