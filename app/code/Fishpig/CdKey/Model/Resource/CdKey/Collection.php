<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 2:19 PM
 */
/*
namespace Fishpig\CdKey\Model\Resource\CdKey;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
     //   \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM1',true));
        $this->_init(
            'Fishpig\CdKey\Model\CdKey',
            'Fishpig\CdKey\Model\Resource\CdKey'
        );
    }
}*/

namespace Fishpig\CdKey\Model\Resource\CdKey;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'tablename';

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
}