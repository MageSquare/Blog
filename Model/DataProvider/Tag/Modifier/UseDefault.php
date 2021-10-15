<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\DataProvider\Tag\Modifier;

use MageSquare\Blog\Api\Data\TagInterface;
use MageSquare\Blog\Model\DataProvider\AbstractModifier;

class UseDefault extends AbstractModifier
{
    /**
     * @return array
     */
    public function getFieldsByStore()
    {
        return TagInterface::FIELDS_BY_STORE;
    }
}
