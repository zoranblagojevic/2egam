<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/9/2016
 * Time: 9:48 AM
 */
namespace Fishpig\CdKey\Block\Key\Email;




class Items extends \Magento\Framework\View\Element\Template
{

    protected $_cdkeyCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Fishpig\CdKey\Resource\Cdkey\CollectionFactory $cdkeyCollectionFactory,
        array $data = [])
    {
        $this->_cdkeyCollectionFactroy = $cdkeyCollectionFactory;
        parent::__construct($context, $data);
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('cdkey/key/email/items.phtml');

        $items = $this->getOrder()->getItemsCollection();
        $keyItems = array();

        foreach($items as $item) {
            if ($keys = $this->_getKeysByItem($item)) {
                foreach($keys as $key) {
                    $keyItems[] = $key;
                }
            }
        }

        $this->setKeys($keyItems);

        return parent::_beforeToHtml();
    }

    /**
     * Retrieve a collection of keys for the item
     *
     * @param Mage_Sales_Model_Order_Item|int $item
     * @return false|Fishpig_CdKey_Model_Mysql4_Key__Collection
     */
    protected function _getKeysByItem($item)
    {
        $itemId = $item;

        if ($item instanceof \Magento\Sales\Model\Order\Item) {
            $itemId = $item->getId();
        }

        /*$keys = Mage::getResourceModel('cdkey/key_collection')
            ->addItemIdFilter($itemId);
*/
        $keys = $this->_cdkeyCollectionFactroy->addItemIdFilter($itemId);

        if($this->getOnlyInvoiced()){
            $keys->addIsInvoicedFilter();
        }

        if($this->getOnlyProduct()){
            $keys->addProductIdFilter($this->getOnlyProduct());
        }

        $keys->load();

        if (count($keys) > 0) {
            return $keys;
        }

        return false;
    }
}
