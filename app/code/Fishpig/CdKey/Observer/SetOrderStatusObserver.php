<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/9/2016
 * Time: 10:53 AM
 */

namespace Fishpig\CdKey\Observer;

class SetOrderStatusObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
    protected $_helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Fishpig\CdKey\Helper\Data $helper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if(!$order->getId())
            return $this;

        //$helper = Mage::helper('cdkey');
        $helper = $this->_helper;

        foreach($order->getAllVisibleItems() as $_item){
            if($helper->isPreOrderEnabled($_item->getProductId())){

                if((in_array($order->getState(), array(\Magento\Sales\Model\Order::STATE_NEW)) && $order->getIsInProcess()))
                {
                    $order->unsIsInProcess();
                    $order->setData('state', \Magento\Sales\Model\Order::STATE_PROCESSING);
                    $order->setStatus('pre_order_paid');

                }else if(in_array($order->getState(), array(\Magento\Sales\Model\Order::STATE_NEW, \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT))){
                    $order->setStatus('pre_order_pending');
                }
                else if(in_array($order->getState(), array(\Magento\Sales\Model\Order::STATE_PROCESSING))){
                    $order->setStatus('pre_order_paid');
                }

                break;
            }
        }
    }

/*
    //sales_order_save_before
    public function setOrderStatus($observer){

        $order = $observer->getEvent()->getOrder();

        if(!$order->getId())
            return $this;

        $helper = Mage::helper('cdkey');

        foreach($order->getAllVisibleItems() as $_item){
            if($helper->isPreOrderEnabled($_item->getProductId())){

                if((in_array($order->getState(), array(Mage_Sales_Model_Order::STATE_NEW)) && $order->getIsInProcess()))
                {
                    $order->unsIsInProcess();
                    $order->setData('state', Mage_Sales_Model_Order::STATE_PROCESSING);
                    $order->setStatus('pre_order_paid');

                }else if(in_array($order->getState(), array(Mage_Sales_Model_Order::STATE_NEW, Mage_Sales_Model_Order::STATE_PENDING_PAYMENT))){
                    $order->setStatus('pre_order_pending');
                }
                else if(in_array($order->getState(), array(Mage_Sales_Model_Order::STATE_PROCESSING))){
                    $order->setStatus('pre_order_paid');
                }

                break;
            }
        }
    }*/
}