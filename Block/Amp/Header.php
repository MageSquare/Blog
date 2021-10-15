<?php
/**
 * @author MageSquare Team
 * @copyright Copyright (c) 2021 MageSquare 
 * @package MageSquare_Blog
 */


namespace MageSquare\Blog\Block\Amp;

use Magento\Framework\View\Element\Template;

/**
 * Class Header
 */
class Header extends \Magento\Theme\Block\Html\Header
{
    /**
     * @var string
     */
    protected $_template = 'MageSquare_Blog::amp/html/header.phtml';

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    private $sessionFactory;

    public function __construct(
        Template\Context $context,
        \Magento\Customer\Model\SessionFactory $sessionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @return string
     */
    public function getCustomerFullname()
    {
        $customer = $this->getCustomerSession()->getCustomer();

        return $customer->getFirstname() . ' ' . $customer->getLastname();
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    private function getCustomerSession()
    {
        return $this->sessionFactory->create();
    }
}
