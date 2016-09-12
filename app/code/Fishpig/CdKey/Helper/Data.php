<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/24/2016
 * Time: 9:20 AM
 */

// @codingStandardsIgnoreFile

namespace Fishpig\CdKey\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Default review helper
 */
class Data extends AbstractHelper
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    protected $_productResourceModel;
    protected $_storeManager;
    protected $_scopeConfig;
    protected $_isPreorderEnabled = array();
    protected $_missing_keys_per_product = array();
    protected $_orderItemCollectionFactory;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $orderItemCollectionFactory
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        $this->_productResourceModel = $productResourceModel;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Get review detail
     *
     * @param string $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br($this->filter->truncate($origDetail, ['length' => 50]));
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->filter->truncate($this->_escaper->escapeHtml($origDetail), ['length' => 50]));
    }

    /**
     * Return an indicator of whether or not guest is allowed to write
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsGuestAllowToWrite()
    {
        return $this->scopeConfig->isSetFlag(self::XML_REVIEW_GUETS_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    public function getCdKeyStatuses()
    {
        return [
            \Fishpig\CdKey\Model\CdKey::STATUS_AVAILABLE => __('Available'),
            \Fishpig\CdKey\Model\CdKey::STATUS_NOT_AVAILABLE => __('Not Available')
        ];
    }

    public function getCdKeyStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getCdKeyStatuses() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }

    public function isPreOrderEnabled($product_id){

        if(isset($this->_isPreorderEnabled[$product_id])) {
            return (bool) $this->_isPreorderEnabled[$product_id];
        }

        //$product_resource = Mage::getResourceModel('catalog/product');
        $product_resource = $this->_productResourceModel;
        $store = $this->_storeManager;
        return (bool) $this->_isPreorderEnabled[$product_id] = $product_resource->getAttributeRawValue($product_id, 'cdkey_is_preorder', $store);

    }

    public function getNumberOfMissingKeys($product_id){

        if(isset($this->_missing_keys_per_product[$product_id])) {
            return (int) $this->_missing_keys_per_product[$product_id];
        }

        //$order_state = Mage::getStoreConfig('cdkey/order_status/processing');
        $order_state = $this->_scopeConfig->getValue('cdkey/order_status/processing', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        //$item_collection = Mage::getResourceModel('sales/order_item_collection');
        $item_collection = $this->_orderItemCollectionFactory;
        $item_collection->addFieldToFilter('product_id', array('eq'=>$product_id));

        $countSelect = $item_collection->getSelect()
            ->joinInner(array('o' => $item_collection->getTable('sales_order')), 'o.entity_id=main_table.order_id', array())
            ->joinLeft(array('keys' => $item_collection->getTable('cdkey_key_order_item')), 'keys.item_id=main_table.item_id', array())
            ->where('o.state =?', $order_state)
            ->where('keys.item_id IS NULL')
        ;
/*
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);*/
        $countSelect->reset(\Zend_Db_Select::ORDER);
        $countSelect->reset(\Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(\Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(\Zend_Db_Select::COLUMNS);
        $countSelect->columns('sum(main_table.qty_ordered)');

        $this->_missing_keys_per_product[$product_id] = (int) $item_collection->getConnection()->fetchOne($countSelect);
        return $this->_missing_keys_per_product[$product_id];
    }
}
