<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 2:18 PM
 */


namespace Fishpig\CdKey\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CdKey extends AbstractDb
{
    protected $_helper;
    protected $_cdkeyCollectionFactory;
    protected $_coreResource;

    public function __construct(
        Context $context,
        $connectionName,
        \Fishpig\CdKey\Helper\Data $helper,
        \Fishpig\CdKey\Resource\Cdkey\CollectionFactory $cdkeyCollectionFactory,
        \Magento\Framework\App\ResourceConnection $coreResource
    )
    {
        $this->_helper = $helper;
        $this->_cdkeyCollectionFactory = $cdkeyCollectionFactory;
        $this->_coreResource = $coreResource;
        parent::__construct($context, $connectionName);
    }

    /**
     * Define main table
     */
    protected function _construct()
    {
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('jjj',true));
       $this->_init('cdkey_key', 'key_id');
    }

    /**
     * Determine whether the product ID exists and is of the correct attribute set
     *
     * @param int $productId
     * @return bool
     */
    public function isProductIdValid($productId)
    {
        if ($productId) {
            //$select = $this->_getReadAdapter()  ///  ['_getReadAdapter', 'Magento\Framework\Model\ResourceModel\Db\AbstractDb', 'getConnection'],
            $select = $this->getConnection()
                ->select()
                //->from($this->getTable('catalog/product'), 'entity_id')
                ->from($this->getTable('catalog_product_entity'), 'entity_id')
                ->where('catalog_product_entity.entity_id=?', $productId)
               // ->where('attribute_set_id=?', Mage::helper('cdkey')->getAttributeSetId())
                ->where('catalog_product_entity.attribute_set_id=4') // replacement for now
                ->limit(1);
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $select,true));
            //return $this->_getReadAdapter()->fetchOne($select) !== false;
            return $this->getConnection()->fetchOne($select) != false;
        }

        return false;
    }

    /**
     * Process the order item
     * Check whether not linked and contains valid product
     *
     */
    public function reserveKeysForOrderItem(\Magento\Sales\Model\Order\Item $item, $place_order = false)
    {
        if ($this->_isItemIdAvailable($item->getId())) {
            $qty = $item->getQtyOrdered();

            $qty_avaliable = $this->getAvailableKeysByProductId($item->getProductId());
            if($qty_avaliable < $qty){
                if(!$place_order){
                    Mage::throwException('There is no enough keys, please upload new keys!');
                }
                return false;
            }

            $reserver_keys = 0;
            for ($i = 1; $i <= $qty; $i++) {
                if ($keyId = $this->_getAvailableKeyIdByProductId($item->getProductId())) {
                    try {
                        $this->getConnection()->insert($this->getKeyOrderItemTable(), array('key_id' => $keyId, 'item_id' => $item->getId()));
                        $reserver_keys++;
                    }
                    catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }

            return $reserver_keys;
        }

        return false;
    }

    /**
     * Determine whether the item ID has already been assigned a key
     *
     * @param int $itemId
     * @return bool
     */
    protected function _isItemIdAvailable($itemId)
    {
        $select = $this->getConnection()
            ->select()
            ->from($this->getKeyOrderItemTable(), 'item_id')
            ->where('cdkey_key_order_item.item_id=?', $itemId)
            ->limit(1);

        return $this->getConnection()->fetchOne($select) === false;
    }

    /**
     * Retrieve the table name for the key_order_item table
     *
     * @return string
     */
    public function getKeyOrderItemTable()
    {
        return $this->getTable('cdkey_key_order_item');
    }

    public function getAvailableKeysByProductId($productId){

        $select = $this->getConnection()
            ->select()
            ->from(array('main_table' => $this->getMainTable()), 'count(*)')
            ->where('`main_table`.`product_id`=?', $productId);

        $select->joinLeft(
            array('key_order_item_table' => $this->getKeyOrderItemTable()),
            '`key_order_item_table`.`key_id`=`main_table`.`key_id`',
            ''
        );

        $select->where('`key_order_item_table`.`item_id` IS NULL');
        return (int) $this->getConnection()->fetchOne($select);
    }

    /**
     * Retrieve an available key ID
     *
     * @param int $productId
     * @return int|false
     */
    protected function _getAvailableKeyIdByProductId($productId)
    {
        $select = $this->getConnection()
            ->select()
            ->from(array('main_table' => $this->getMainTable()), 'key_id')
            ->where('`main_table`.`product_id`=?', $productId)
            ->limit(1);

        $select->joinLeft(
            array('key_order_item_table' => $this->getKeyOrderItemTable()),
            '`key_order_item_table`.`key_id`=`main_table`.`key_id`',
            ''
        );

        $select->where('`key_order_item_table`.`item_id` IS NULL');

        $select->order('main_table.key_id ASC');

        return $this->getConnection()->fetchOne($select);
    }

    public function releaseKey($key)
    {
        $this->getConnection()
            ->delete($this->getKeyOrderItemTable(), $this->getConnection()->quoteInto('key_id=?', $key->getId()));

        $this->_updateProductQtyByKey($key->getProductId());
    }

    /**
     * Update the associate product QTY
     *
     * @param Fishpig_CdKey_Model_Key $key
     * @return $this
     */
    public function _updateProductQtyByKey($product_id)
    {

        $preorder = false;
        $helper = $this->_helper;

        if($helper->isPreOrderEnabled($product_id)){
            $preorder = true;
        }

        $missing_keys = $helper->getNumberOfMissingKeys($product_id);
/*
        $keys = Mage::getResourceModel('cdkey/key_collection')
            ->addProductIdFilter($product_id)
            ->addIsAvailableFilter()
            ->load();
  */
        $keys = $this->_cdkeyCollectionFactory
            ->addProductIdFilter($product_id)
            ->addIsAvailableFilter()
            ->load();

        $keyCount  = $keys->count();

        $newKeyCount = $keyCount-$missing_keys;

        if($preorder){
            $dataItem = array(
                'qty' => $newKeyCount,
                //'is_in_stock' => (int)($keyCount > 0)
            );

            $dataStatus = array(
                'qty' => $newKeyCount,
                //'stock_status' => $dataItem['is_in_stock'],
            );

        }else{

            $dataItem = array(
                'qty' => $newKeyCount,
                'is_in_stock' => (int)($keyCount > 0)
            );

            $dataStatus = array(
                'qty' => $newKeyCount,
                'stock_status' => $dataItem['is_in_stock'],
            );

        }


        $cond = $this->getConnection()->quoteInto('product_id=?', $product_id);

        $this->getConnection()->update($this->getTable('cataloginventory_stock_item'), $dataItem, $cond);
        $this->getConnection()->update($this->getTable('cataloginventory_stock_status'), $dataStatus, $cond);
       // $this->getConnection()->update(Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_status_idx'), $dataStatus, $cond);
        $this->getConnection()->update($this->_coreResource->getTableName('cataloginventory_stock_status_idx'), $dataStatus, $cond);
    }


}