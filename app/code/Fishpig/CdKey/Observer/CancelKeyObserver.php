<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/9/2016
 * Time: 11:19 AM
 */

namespace Fishpig\CdKey\Observer;

class CancelKeyObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
    protected $_cdkeyCollectionFactory;
    protected $_cdkeyResourceModel;
//    protected $_cdkeyResourceModel;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Fishpig\CdKey\Resource\Cdkey\CollectionFactory $cdkeyCollectionFactory,
        \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
  //      \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_cdkeyCollectionFactroy = $cdkeyCollectionFactory;
        $this->_cdkeyResourceModel = $cdKeyResourceModel;
   //     $this->_cdkeyResourceModel = $cdKeyResourceModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if(	$order->getid()
            && $order->getState() == \Magento\Sales\Model\Order::STATE_CANCELED
            && ($order->getOrigData('state') != $order->getState())
        ){


            $keys = array();
            foreach($order->getItemsCollection() as $item) {
                $keys[] = $item->getId();
            }

           /* $keys = Mage::getResourceModel('cdkey/key_collection')
                ->selectKeys($keys);*/
            $keys = $this->_cdkeyCollectionFactroy->selectKeys($keys);

            foreach($keys->getItems() as $key){
                $this->_cdkeyResourceModel->releaseKey($key);
            }
        }
    }

/*sales_order_save_after
    public function cancelKeys($observer){

        $order = $observer->getEvent()->getOrder();

        if(	$order->getid()
            && $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED
            && ($order->getOrigData('state') != $order->getState())
        ){


            $keys = array();
            foreach($order->getItemsCollection() as $item) {
                $keys[] = $item->getId();
            }

            $keys = Mage::getResourceModel('cdkey/key_collection')
                ->selectKeys($keys);

            foreach($keys->getItems() as $key){
                $this->_getResourceModel()->releaseKey($key);
            }
        }
    }*/
}