<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 9:50 AM
 */
namespace Fishpig\CdKey\Block\Adminhtml\Product\Edit\Tab;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class Test extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'catalog/product/edit/test.phtml';

    protected $_coreRegistry = null;

    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
}