<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/9/2016
 * Time: 1:53 PM
 */

namespace Fishpig\CdKey\Observer;

class SetRefundObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
    // protected $_cdkeyResourceModel;
    protected $_coreResource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\ResourceConnection $coreResource
        //   \Fishpig\CdKey\Model\Resource\CdKey $cdKeyResourceModel
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        // $this->_cdkeyResourceModel = $cdKeyResourceModel;
        $this->_coreResource = $coreResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        if($this->_registry->registry('_need_refund_keys') == false){
            return $this;
        }

        $creditmemoItem = $observer->getEvent()->getCreditmemoItem();

//        $resource = Mage::getModel('core/resource');
        $resource =  $this->_coreResource;
        $connection = $resource->getConnection('core_write');


        $sql = "DELETE FROM ".$resource->getTableName('cdkey_key_order_item')." 
		WHERE item_id = '".$creditmemoItem->getOrderItemId()."' LIMIT ".$creditmemoItem->getQty();

        $connection->raw_query($sql);

    }

    /*

//creditmemo_item_save_after
	public function refundKeys($observer){


		if($this->_need_refund_keys == false){
			return $this;
		}


		$creditmemoItem = $observer->getEvent()->getCreditmemoItem();

		$resource = Mage::getModel('core/resource');
    	$connection = $resource->getConnection('core_write');


    	$sql = "DELETE FROM ".$resource->getTableName('cdkey/key_order_item')."
		WHERE item_id = '".$creditmemoItem->getOrderItemId()."' LIMIT ".$creditmemoItem->getQty();

		$connection->raw_query($sql);

	}


     */

}