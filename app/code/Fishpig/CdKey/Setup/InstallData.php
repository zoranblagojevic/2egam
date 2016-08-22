<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/16/2016
 * Time: 10:08 AM
 */

namespace Fishpig\CdKey\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface{
    private $eavSetupFactory;
//    private $salesSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory/*, SalesSetupFactory $salesSetupFactory*/){
        $this->eavSetupFactory = $eavSetupFactory;
//        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context){
        $setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup'=>$setup]);

//        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM',true));


        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cdkey_is_preorder',
            [
                'type'                      => 'int',
                'backend'                   => '',
                'frontend'                  => '',
                'label'                     => 'Pre-order/Backorder',
                'input'                     => 'select',
                'class'                     => '',
                'source'                    =>'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'global'                    => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible'                   => false,
                'required'                  => true,
                'user_defined'              => false,
                'default'                   => '0',
                'searchable'                => false,
                'filterable'                => false,
                'comparable'                => false,
                'visible_on_front'          => false,
                'used_in_prouduct_listing'  => true,
                'unique'                    => false,
                'apply_to'                  => implode(',', \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Model\Product\Type')->getOptionArray()),
                'is_configurable'           => false
            ]
        );
/*
        $statusTable = $setup->getTable('sales/order_status');
        //$statusStateTable   = $installer->getTable('sales/order_status_state');

        $data = array();

        $data[] = array(
            'status'    => 'pre_order_pending',
            'label'     => 'Pre-Order Pending'
        );

        $data[] = array(
            'status'    => 'pre_order_paid',
            'label'     => 'Pre-Order Paid'
        );

        $installer->getConnection()->insertArray($statusTable, array('status', 'label'), $data);
  */
        //$salesSetup = $this->salesSetupFactory->create(['setup'=>$setup]);
        $data = [];
        $statuses = [
            'pre_order_pending' => __('Pre-Order Pending'),
            'pre_order_paid' => __('Pre-Order Paid'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);


        $setup->endSetup();
    }
}