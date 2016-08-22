<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/10/2016
 * Time: 10:59 AM
 */
namespace Fishpig\CdKey\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context){

        //$ttt = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Catalog\Model\Product\Type')->getOptionArray();

///        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(\Magento\Catalog\Model\Product\Type::getOptionArray(),true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($ttt,true));


        $setup->startSetup();

        if(!$setup->tableExists('cdkey_key')) {
            $table = $setup->getConnection()->newTable($setup->getTable('cdkey_key'));

            $table->addColumn('key_id',
                Table::TYPE_INTEGER,
                '11',
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Key Id')
                ->addColumn('product_id',
                    Table::TYPE_INTEGER,
                    '11',
                    ['unsigned' => true, 'nullable' => false, 'default' => 0],
                    'Product Id')
                ->addColumn('key',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Key')
                ->addColumn('is_free',
                    Table::TYPE_INTEGER,
                    1,
                    ['unsigned' => true, 'nullable' => false, 'default' => 1],
                    'Is Free')
                ->addIndex(
                    $setup->getIdxName('cdkey_key', ['key'], AdapterInterface::INDEX_TYPE_UNIQUE),
                    ['key'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->addIndex(
                    $setup->getIdxName('cdkey_key', ['product_id']),
                    ['product_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'cdkey_key',
                        'product_id',
                        'catalog_product_entity',
                        'entity_id'
                    ),
                    'product_id',
                    $setup->getTable('catalog_product_entity'),
                    'entity_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE);

            $table->setComment('CD Key: Keys');
            //$afro = $table->getColumns(false);

            //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($afro,true));
            //$afro['PRODUCT_ID']['LENGTH'] = 11;
            //$table->setColumn($afro['PRODUCT_ID']);
            //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($afro,true));
            //$this->_logger->addDebug('test');
            $setup->getConnection()->createTable($table);
        }

        if(!$setup->tableExists('cdkey_key_order_item')) {
            $table = $setup->getConnection()->newTable($setup->getTable('cdkey_key_order_item'));

            $table->addColumn('key_id',
                Table::TYPE_INTEGER,
                '11',
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Key Id')
                ->addColumn('item_id',
                    Table::TYPE_INTEGER,
                    '11',
                    ['unsigned' => true, 'nullable' => false, 'default' => 0],
                    'Item Id')
                ->addIndex(
                    $setup->getIdxName('cdkey_key_order_item', ['item_id']),
                    ['item_id']
                )
                ->addForeignKey(
                    $setup->getFkName(
                        'cdkey_key_order_item',
                        'item_id',
                        'sales_order_item',
                        'item_id'
                    ),
                    'item_id',
                    $setup->getTable('sales_order_item'),
                    'item_id',
                    Table::ACTION_CASCADE,
                    Table::ACTION_CASCADE);

            $table->setComment('CD Key: Key/Order Item links');

            $setup->getConnection()->createTable($table);

            $setup->getConnection()->addIndex(
                $setup->getTable('sales_order_item'),
                $setup->getIdxName('sales_order_item', ['product_id']),
                'product_id'
            );
        }
        $setup->endSetup();
    }
}