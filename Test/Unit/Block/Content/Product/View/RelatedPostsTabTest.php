<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Test\Unit\Block\Content\Product\View;

use MageSquare\Blog\Block\Content\Product\View\RelatedPostsTab;
use MageSquare\Blog\Test\Unit\Traits\ObjectManagerTrait;
use MageSquare\Blog\Test\Unit\Traits\ReflectionTrait;
use MageSquare\Blog\ViewModel\Product\View\RelatedPosts;
use Magento\Framework\DataObject\IdentityInterface;
use PHPUnit\Framework\TestCase;

/**
 *
 * @see \MageSquare\Blog\Block\Content\Product\View\RelatedPostsTab
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * phpcs:ignoreFile
 */
class RelatedPostsTabTest extends TestCase
{
    use ObjectManagerTrait;
    use ReflectionTrait;

    /**
     * @covers       \MageSquare\Blog\Block\Content\Product\View\RelatedPostsTab::getIdentities
     * @dataProvider getIdentitiesDataProvider
     *
     * @param array $cacheTags
     * @param array $expectedResult
     */
    public function testGetIdentities(array $cacheTags, array $expectedResult): void
    {
        $posts = [];

        foreach ($cacheTags as $cacheTag) {
            $post = $this->getMockForAbstractClass(IdentityInterface::class);
            $post->expects($this->any())->method('getIdentities')->willReturn($cacheTag);
            $posts[] = $post;
        }

        $viewModel = $this->createMock(RelatedPosts::class);
        $viewModel->expects($this->any())->method('getPostsForCurrentProduct')->willReturn($posts);

        $block = $this->getObjectManager()->getObject(
            RelatedPostsTab::class,
            ['data' => ['view_model' => $viewModel]]
        );

        $this->assertEquals($expectedResult, $block->getIdentities());
    }

    public function getIdentitiesDataProvider(): array
    {
        return [
            [
                [
                    [
                        'cache_tag_1',
                        'cache_tag_2'
                    ],
                    [
                        'cache_tag_3'
                    ]
                ],
                [
                    'cache_tag_1',
                    'cache_tag_2',
                    'cache_tag_3'
                ]
            ],
            [
                [],
                []
            ]
        ];
    }
}
