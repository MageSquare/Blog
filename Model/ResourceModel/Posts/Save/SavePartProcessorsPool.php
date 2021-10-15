<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\ResourceModel\Posts\Save;

use Generator;
use Traversable;

class SavePartProcessorsPool implements \IteratorAggregate
{
    /**
     * @var SavePartInterface[]
     */
    private $savePartProcessorsQueue;

    /**
     * savePartQueue must be created in the specified format
     *
     * @example [
     *     '0' => [
     *          'sortOrder' => 10,
     *          'processor' => $processor
     *      ]
     * ]
     *
     * @param array $savePartQueue
     */
    public function __construct(
        array $savePartQueue = []
    ) {
        $this->savePartProcessorsQueue = $savePartQueue;
    }

    /**
     * @return Generator<SavePartInterface>
     */
    public function getIterator(): Traversable
    {
        $preparedQueue = $this->sortConfigs($this->filterConfigs($this->savePartProcessorsQueue));

        yield from array_column($preparedQueue, 'processor');
    }

    private function filterConfigs(array $configs): array
    {
        return array_filter($configs, function (array $configPart): bool {
            return $configPart['processor'] instanceof SavePartInterface;
        });
    }

    private function sortConfigs(array $configs): array
    {
        usort($configs, function (array $processorConfigA, array $processorConfigB): int {
            return (int)$processorConfigA['sortOrder'] <=> (int)$processorConfigB['sortOrder'];
        });

        return $configs;
    }
}
