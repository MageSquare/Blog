<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


declare(strict_types=1);

namespace MageSquare\Blog\Model\Observer;

use MageSquare\Blog\Model\Notification\Notification;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class NotifyCustomers implements ObserverInterface
{
    /**
     * @var Notification
     */
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function execute(Observer $observer): void
    {
        $this->notification->notifyCustomersReplies($observer->getData('comment'));
    }
}
