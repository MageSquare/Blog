<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Image;

use MageSquare\Blog\Model\ImageProcessor;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class ImagePathConverter
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getImagePath(string $image): string
    {
        if (strpos($image, '/') === false) {
            $mediaPath = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            $imagePath = $mediaPath . ImageProcessor::BLOG_MEDIA_PATH . '/' . $image;
        } else {
            $imagePath = $this->storeManager->getStore()->getBaseUrl() . trim($image, '/');
        }

        return $imagePath;
    }
}
