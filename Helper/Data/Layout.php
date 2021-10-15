<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Helper\Data;

/**
 * Class Layout
 */
class Layout extends \MageSquare\Blog\Helper\Config
{
    const CONFIG_PATH_LAYOUT = 'layout/%s';

    const CONFIG_PATH_LAYOUT_VALUE = 'layout/%s/%s';

    /**
     * Retrieves Blocks from Config
     *
     * @param $type 'content' | 'sidebar'
     *
     * @return array
     */
    public function getBlocks($type)
    {
        $values = [];
        $config = $this->getConfig();

        if (isset($config[$type])) {
            foreach ($config[$type] as $key => &$item) {
                $item['value'] = $key;
            }

            $values = $config[$type];
        }

        return $values;
    }
}
