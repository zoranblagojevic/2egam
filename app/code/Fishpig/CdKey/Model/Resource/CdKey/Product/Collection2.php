<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/23/2016
 * Time: 3:09 PM
 */

namespace Fishpig\CdKey\Model\Resource\CdKey\Product;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection2 extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    //const YOUR_TABLE = 'tablename';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Fishpig\CdKey\Model\CdKey',
            'Fishpig\CdKey\Model\Resource\CdKey'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = secondTable.entity_id',
            ['sku']
        );

        // \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $this->getSelect(),true));
    }

    public function addEntityFilter($entityId)
    {
        //$this->getSelect()->where('rt.entity_pk_value = ?', $entityId);
        return $this;
    }

    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }
}