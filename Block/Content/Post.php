<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Block\Content;

use MageSquare\Blog\Model\Blog\Registry;
use MageSquare\Blog\Model\NetworksFactory;
use Magento\Cms\Model\Template\Filter;
use Magento\Framework\DataObject\IdentityInterface;

class Post extends AbstractBlock implements IdentityInterface
{
    /**
     * @var \MageSquare\Blog\Model\PostsFactory
     */
    private $postRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var NetworksFactory
     */
    private $networksModel;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    private $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \MageSquare\Blog\Helper\Data $dataHelper,
        Registry $registry,
        \MageSquare\Blog\Helper\Settings $settingsHelper,
        \MageSquare\Blog\Helper\Url $urlHelper,
        Filter $filter,
        \MageSquare\Blog\Api\PostRepositoryInterface $postRepository,
        NetworksFactory $networksModel,
        \MageSquare\Blog\Model\UrlResolver $urlResolver,
        \MageSquare\Blog\Helper\Date $helperDate,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Session $session,
        array $data = []
    ) {
        parent::__construct($context, $dataHelper, $settingsHelper, $urlHelper, $urlResolver, $helperDate, $data);
        $this->postRepository = $postRepository;
        $this->registry = $registry;
        $this->filter = $filter;
        $this->networksModel = $networksModel;
        $this->jsonEncoder = $jsonEncoder;
        $this->session = $session;
    }

    /**
     * @return \MageSquare\Blog\Model\Posts|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPost()
    {
        $post = $this->registry->registry(Registry::CURRENT_POST);
        if (!$post) {
            $this->session->start();
            $postId = $this->getRequest()->getParam('id') ?: $this->session->getCurrentPostId();
            if ($postId) {
                $post = $this->postRepository->getById($postId);
                $this->registry->register(Registry::CURRENT_POST, $post, true);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Unknown post id.'));
            }
        }

        return $post;
    }

    /**
     * @return array
     */
    public function getJsonMicroData()
    {
        $resultArray = [$this->jsonEncoder->encode($this->generateMainMicroData())];

        $breadCrumbItems = $this->getBreadCrumbData();
        if ($breadCrumbItems) {
            $resultArray[] = $this->jsonEncoder->encode(
                [
                    '@context'        => 'http://schema.org',
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => $breadCrumbItems
                ]
            );
        }

        return $resultArray;
    }

    /**
     * @return array
     */
    private function generateMainMicroData()
    {
        $main = [
            '@context'      => 'http://schema.org',
            '@type'         => 'BlogPosting',
            'author'        => [
                "@type" => 'Person',
                "name"  => $this->escapeHtml($this->getPost()->getPostedBy() ?: 'undefined')
            ],
            'datePublished' => $this->escapeHtml($this->getPost()->getPublishedAt()),
            'dateModified'  => $this->escapeHtml($this->getPost()->getUpdatedAt()),
            'name'          => $this->escapeHtml($this->getPost()->getTitle()),
            'description'   => $this->escapeHtml($this->getPost()->getShortContent()),
            'image'         => $this->getPost()->getPostImageSrc(),
            'mainEntityOfPage'
                            => $this->escapeUrl($this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true])),
            'headline'      => $this->escapeHtml($this->getPost()->getTitle())
        ];

        $orgName = $this->getSettingHelper()->getModuleConfig('search_engine/organization_name');
        if ($orgName) {
            $main['publisher'] = [
                "@type" => 'Organization',
                'url'   => $this->_urlBuilder->getBaseUrl(),
                "name"  => $this->escapeHtml($orgName)
            ];

            $logoBlock = $this->getLayout()->getBlock('logo');
            if ($logoBlock) {
                $main['publisher']['logo'] = $logoBlock = $logoBlock->getLogoSrc();
            }
        }

        return $main;
    }

    /**
     * @return array
     */
    private function getBreadCrumbData()
    {
        $items = [];
        $position = 0;
        $breadcrumbs = $this->getCrumbs();
        foreach ($breadcrumbs as $breadcrumb) {
            if (!isset($breadcrumb['link']) || !$breadcrumb['link']) {
                continue;
            }

            $items []= [
                '@type' => 'ListItem',
                'position' => ++$position,
                'item' => [
                    '@id' => $breadcrumb['link'],
                    'name' => $breadcrumb['label']
                ]
            ];
        }

        return $items;
    }

    /**
     * @return AbstractBlock|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareBreadcrumbs()
    {
        parent::prepareBreadcrumbs();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $this->addCrumb(
                $breadcrumbs,
                'post',
                [
                    'label' => $this->getPost()->getTitle(),
                    'title' => $this->getPost()->getTitle(),
                ]
            );
        }
    }

    /**
     * @return string
     */
    public function getSocialHtml()
    {
        return $this->getChildHtml('msblog_social');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getHelpfulHtml()
    {
        $html = '';
        $block = $this->getChildBlock('msblog_helpful');
        if ($block) {
            $block->setPost($this->getPost());
            $html = $block->toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getColorClass()
    {
        return $this->getSettingHelper()->getIconColorClass();
    }

    /**
     * @return bool
     */
    public function getShowPrintLink()
    {
        return $this->getSettingHelper()->getShowPrintLink();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function hasThumbnailUrl()
    {
        $post = $this->getPost();
        if ($post) {
            return (bool)$post->getThumbnailUrl();
        }

        return false;
    }

    /**
     * @return mixed|string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getThumbnailUrl()
    {
        $url = '';
        $post = $this->getPost();
        if ($post) {
            $url = $post->getThumbnailUrl();
            $url = $this->filter->filter($url);
        }

        return $url;
    }

    /**
     * @return array|string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getIdentities()
    {
        return [\MageSquare\Blog\Model\Posts::CACHE_TAG . '_' . $this->getPost()->getId()];
    }

    /**
     * @return mixed
     */
    public function getNetworksModel()
    {
        return $this->networksModel->create();
    }

    /**
     * @return bool
     */
    public function getUseCommentsGlobal()
    {
        return $this->getSettingHelper()->getUseComments();
    }

    public function isShowViewsCounter(): bool
    {
        return $this->getSettingHelper()->getDisplayViews();
    }
}
