<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model;

/**
 * Class
 */
class Networks
{
    /**
     * @var array
     */
    private $networks;

    /**
     * @var \MageSquare\Blog\Helper\Data
     */
    private $data;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    private $objectFactory;

    public function __construct(
        \MageSquare\Blog\Helper\Data $data,
        \MageSquare\Blog\Model\Config\Source\Networks $networksSource,
        \Magento\Framework\DataObjectFactory $objectFactory
    ) {
        $this->data = $data;
        $this->objectFactory = $objectFactory;
        $this->networks = $networksSource->getArray();
    }

    /**
     * @param $network
     * @return bool
     */
    private function isEnabled($network)
    {
        if (in_array($network, $this->data->getSocialNetworks())) {
            return true;
        }

        return false;
    }

    /**
     * Enabled Networks
     *
     * @return array
     */
    public function getNetworks()
    {
        $networks = [];
        foreach ($this->networks as $data) {
            if ($this->isEnabled($data['value'])) {
                $networks[] = $this->objectFactory->create(['data' => $data]);
            }
        }

        return $networks;
    }
}
