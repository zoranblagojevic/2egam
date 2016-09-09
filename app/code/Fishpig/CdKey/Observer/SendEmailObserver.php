<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/8/2016
 * Time: 10:10 AM
 */

namespace Fishpig\CdKey\Observer;

class SendEmailObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
 //   protected $_cdkeyResourceModel;
    protected $_observerHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
  //      \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
        \Fishpig\CdKey\Helper\ObserverHelper $observerHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
//        $this->_cdkeyResourceModel = $cdKeyResourceModel;
        $this->_observerHelper = $observerHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        try{
            $this->_observerHelper->sendOrderEmail($order, false);
        }catch(Exception $e){

        }
        return $this;
    }
/*
public function sendEmail($observer){

    $order = $observer->getEvent()->getOrder();
    try{
        $this->sendOrderEmail($order, false);
    }catch(Exception $e){

    }
    return $this;
}
*/

/**
 * Send an email for all CD keys
 *
 * @param Mage_Sales_Model_Order $order
 * @return bool
 */





}