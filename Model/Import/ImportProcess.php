<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Model\Import;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ImportProcess
 */
class ImportProcess
{
    /**
     * @var array
     */
    private $imports;

    public function __construct($imports = [])
    {
        $this->imports = $imports;
    }
    
    public function processImport()
    {
        /** @var \MageSquare\Blog\Model\Import\AbstractImport $import */
        foreach ($this->imports as $import) {
            $import->processImport();
        }
    }
}
