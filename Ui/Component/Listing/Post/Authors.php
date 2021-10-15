<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Ui\Component\Listing\Post;

use Magento\Framework\Data\OptionSourceInterface;

class Authors implements OptionSourceInterface
{
    /**
     * @var \MageSquare\Blog\Api\AuthorRepositoryInterface
     */
    private $authorRepository;

    public function __construct(\MageSquare\Blog\Api\AuthorRepositoryInterface $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $collection = $this->authorRepository->getAuthorCollection();
        foreach ($collection as $author) {
            $options[] = [
                'label' => $author->getName(),
                'value' => $author->getAuthorId()
            ];
        }

        return $options;
    }
}
