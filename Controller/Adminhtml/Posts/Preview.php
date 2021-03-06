<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Controller\Adminhtml\Posts;

use MageSquare\Blog\Api\Data\PostInterface;
use MageSquare\Blog\Model\Repository\PostRepository;
use MageSquare\Blog\Model\ResourceModel\Posts\RelatedProducts\GetPostRelatedProductsForPreview;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\MessageInterface;
use MageSquare\Blog\Model\PostsFactory;
use MageSquare\Blog\Model\Source\PostStatus;

class Preview extends \Magento\Backend\App\Action
{
    /**
     * @var PostsFactory
     */
    private $postsFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \MageSquare\Blog\Helper\Date
     */
    private $helperDate;

    /**
     * @var PostRepository
     */
    private $repository;

    /**
     * @var \Magento\Framework\Url
     */
    private $urlHelper;

    /**
     * @var \Magento\Framework\App\Cache\Type\Block
     */
    private $cache;

    /**
     * @var \MageSquare\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $mathRandom;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \MageSquare\Blog\Model\PostsFactory $postsFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \MageSquare\Blog\Helper\Date $helperDate,
        \MageSquare\Blog\Model\Repository\PostRepository $repository,
        \Magento\Framework\Url $urlHelper,
        \Magento\Framework\App\Cache\Type\Block $cache,
        \MageSquare\Base\Model\Serializer $serializer,
        \Magento\Framework\Math\Random $mathRandom
    ) {
        parent::__construct($context);
        $this->postsFactory = $postsFactory;
        $this->dateTime = $dateTime;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helperDate = $helperDate;
        $this->repository = $repository;
        $this->urlHelper = $urlHelper;
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->mathRandom = $mathRandom;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $messages = [];
        $data = $this->getPostData();
        if ($data) {
            try {
                $post = $this->postsFactory->create(['data' => $data]);
                $this->prepareForView($post);
                $key = $this->savePostData($post->getData());
            } catch (\Exception $e) {
                $messages[] = __('An error occurred while execution');
                $messages[] = $e->getMessage();
            }
        } else {
            $messages[] = __('Empty Data for the post for preview');
        }

        if ($this->getRequest()->isAjax()) {
            if (!empty($messages)) {
                return $this->resultJsonFactory->create()->setData(
                    [
                        'messages' => $messages,
                        'error'    => true
                    ]
                );
            }

            return $this->resultJsonFactory->create()->setData([
                'url' => $this->urlHelper->getUrl('msblog/post/preview', ['msblog_key' => $key])
            ]);
        } else {
            if (!empty($messages)) {
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage($message);
                }

                return $this->_redirect('*/*/');
            }

            $url = $this->urlHelper->getUrl('msblog/post/preview', ['msblog_key' => $key]);
            return $this->_redirect($url);
        }
    }

    /**
     * @param array $data
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function savePostData($data)
    {
        $key = $this->mathRandom->getRandomString(16);
        $data = $this->serializer->serialize($data);
        $this->cache->save($data, $key, ['msblog_preview']);

        return $key;
    }

    /**
     * @return array
     */
    protected function getPostData()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $postId = $this->getRequest()->getParam('id', false);
            if ($postId) {
                try {
                    $post = $this->repository->getById($postId);
                    $post->setData(GetPostRelatedProductsForPreview::IS_PREVIEW_FROM_SAVED_FLAG, true);
                    $data = $post->getData();
                } catch (NoSuchEntityException $e) {
                    $data = [];
                }
            }
        }
        return $data;
    }

    /**
     * @param $model
     */
    private function prepareForView($model)
    {
        $this->prepareImage($model, PostInterface::POST_THUMBNAIL);
        $this->prepareStatus($model);
    }

    /**
     * @param $model
     */
    public function prepareStatus($model)
    {
        $model->setStatus(PostStatus::STATUS_ENABLED);
        if (!$model->getPublishedAt()) {
            $currentTimestamp = $this->dateTime->gmtTimestamp();
            $model->setPublishedAt(date('Y-m-d H:i:s', $currentTimestamp));
        } else {
            $convertedDate = $this->helperDate->renderDate(date('Y-m-d H:i:s', strtotime($model->getPublishedAt())));
            $model->setPublishedAt($convertedDate);
        }
    }

    /**
     * @param $model
     * @param $imageName
     */
    private function prepareImage($model, $imageName)
    {
        $fileName = $imageName . '_file';
        $thumbnail = $model->getData($fileName);
        if (isset($thumbnail) && is_array($thumbnail)) {
            if (isset($thumbnail[0]['name']) && isset($thumbnail[0]['tmp_name'])) {
                $model->setData($imageName, 'tmp/' . $thumbnail[0]['name']);
            }
        }
    }
}
