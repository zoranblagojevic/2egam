<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/7/2016
 * Time: 2:58 PM
 */

namespace Fishpig\CdKey\Observer;

class ReserveKeyFlagObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
    protected $_cdkeyResourceModel;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_cdkeyResourceModel = $cdKeyResourceModel;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_registry->registry('_reserve_keys') == false){
            return $this;
        }

        $orderItem = $observer->getEvent()->getItem();
        //$helper = Mage::helper('cdkey');

        //\Fishpig\CdKey\Model\Resource
         $this->_cdkeyResourceModel;


        if ( $this->_cdkeyResourceModel->isProductIdValid($orderItem->getProductId())) {
            try{
                $this->_cdkeyResourceModel->reserveKeysForOrderItem($orderItem, true);
            }catch(Exception $e){

            }
        }
    }

    /* 	public function reserveKeys(Varien_Event_Observer $observer)
	{
		if($this->_reserve_keys == false){
			return $this;
		}

		$orderItem = $observer->getEvent()->getItem();
		$helper = Mage::helper('cdkey');

		if ($this->_getResourceModel()->isProductIdValid($orderItem->getProductId())) {
				try{
					$this->_getResourceModel()->reserveKeysForOrderItem($orderItem, true);
				}catch(Exception $e){

				}
		}
	}*/
}