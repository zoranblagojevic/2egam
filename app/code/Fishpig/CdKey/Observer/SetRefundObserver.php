<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/9/2016
 * Time: 1:48 PM
 */

namespace Fishpig\CdKey\Observer;

class SetRefundObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
   // protected $_cdkeyResourceModel;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
     //   \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
       // $this->_cdkeyResourceModel = $cdKeyResourceModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditmemoItem = $observer->getEvent()->getCreditmemoItem();

        if (!$creditmemoItem->getId()) {
            $this->_registry->register('_need_refund_keys',true);
            //$this->_need_refund_keys = true;
        } else {
            $this->_registry->register('_need_refund_keys',false);
            //$this->_need_refund_keys = false;
        }

        return $this;
    }

    /*

    protected $_need_refund_keys = false;

//creditmemo_item_save_before
public function setRefundFlag($observer)
{
    $creditmemoItem = $observer->getEvent()->getCreditmemoItem();

    if (!$creditmemoItem->getId()) {
        $this->_need_refund_keys = true;
    } else {
        $this->_need_refund_keys = false;
    }

    return $this;
}



     */

}