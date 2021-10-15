<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\ViewModel\Posts;

use MageSquare\Blog\Api\Data\GetPostRelatedProductsInterface;
use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\ConfigProvider;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Block\Product\ReviewRendererInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Pricing\Render as PriceRender;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;

class RelatedProducts implements ArgumentInterface
{
    const IMAGE_ID = 'magesquare_blog_related_products_thumbnail';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var GetPostRelatedProductsInterface
     */
    private $getPostRelatedProducts;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var ReviewRendererInterface
     */
    private $reviewRenderer;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var PriceRender
     */
    private $priceRenderer;

    public function __construct(
        Registry $registry,
        GetPostRelatedProductsInterface $getPostRelatedProducts,
        ConfigProvider $configProvider,
        ImageFactory $imageFactory,
        ReviewRendererInterface $reviewRenderer,
        LayoutInterface $layout
    ) {
        $this->registry = $registry;
        $this->getPostRelatedProducts = $getPostRelatedProducts;
        $this->configProvider = $configProvider;
        $this->imageFactory = $imageFactory;
        $this->reviewRenderer = $reviewRenderer;
        $this->layout = $layout;
    }

    public function getCurrentPost(): PostInterface
    {
        return $this->registry->registry(Registry::CURRENT_POST);
    }

    public function getPostProducts(): array
    {
        $post = $this->getCurrentPost();

        return $this->getPostRelatedProducts->execute($post->getPostId());
    }

    public function getRelatedProductsBlockName(): string
    {
        return $this->configProvider->getPostPageBlockTitleOnPostPage();
    }

    public function isCanRender(): bool
    {
        return count($this->getPostProducts()) > 0;
    }

    public function getImageHtml(Product $product): ?string
    {
        return $this->imageFactory->create($product, self::IMAGE_ID, [])->toHtml();
    }

    public function getReviewsHtml(Product $product): ?string
    {
        return $this->reviewRenderer->getReviewsSummaryHtml($product, ReviewRendererInterface::SHORT_VIEW);
    }

    private function getPriceRenderer(): PriceRender
    {
        if (!$this->priceRenderer) {
            $this->priceRenderer = $this->layout->createBlock(
                PriceRender::class,
                '',
                ['data' => [
                    'price_render_handle' => 'catalog_product_prices',
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]]
            );
        }

        return $this->priceRenderer;
    }

    public function getProductPriceHtml(Product $product): ?string
    {
        return $this->getPriceRenderer()->render(FinalPrice::PRICE_CODE, $product);
    }
}
