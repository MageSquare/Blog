<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Layout;

use MageSquare\Blog\Api\Data\AuthorInterface;
use MageSquare\Blog\Model\AuthorService;
use MageSquare\Blog\Model\Image\ImagePathConverter;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;

class AuthorInfo extends AbstractClass
{
    const AUTHOR_PAGE_ACTION = 'author';

    /**
     * @var string
     */
    protected $_template = 'MageSquare_Blog::sidebar/author_info.phtml';

    /**
     * @var AuthorService
     */
    private $authorService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImagePathConverter
     */
    private $imagePathConverter;

    public function __construct(
        Context $context,
        AuthorService $authorService,
        LoggerInterface $logger,
        ImagePathConverter $imagePathConverter,
        array $data = []
    ) {
        $this->authorService = $authorService;
        $this->logger = $logger;
        parent::__construct($context, $data);
        $this->imagePathConverter = $imagePathConverter;
    }

    public function getAuthorData(): ?AuthorInterface
    {
        $request = $this->getRequest();
        $authorId = $request->getActionName() == self::AUTHOR_PAGE_ACTION ? (int)$request->getParam('id') : null;
        $author = $this->authorService->getCurrentAuthor($authorId);

        return $author ? $this->prepareImageUrl($author) : null;
    }

    private function prepareImageUrl(AuthorInterface $author): AuthorInterface
    {
        $image = $author->getImage();
        if ($image && strpos($image, $this->_storeManager->getStore()->getBaseUrl()) === false) {
            try {
                $author->setImage($this->imagePathConverter->getImagePath($image));
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }

        return $author;
    }
}
