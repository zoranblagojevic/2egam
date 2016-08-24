<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/24/2016
 * Time: 10:10 AM
 */

namespace Fishpig\CdKey\Model\Resource\CdKey\Product;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DB\Select;

/**
 * Review Product Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    /**
     * Entities alias
     *
     * @var array
     */
    protected $_entitiesAlias = [];

    /**
     * Review store table
     *
     * @var string
     */
    //protected $_reviewStoreTable;

    /**
     * Add store data flag
     *
     * @var bool
     */
    protected $_addStoreDataFlag = false;

    /**
     * Filter by stores for the collection
     *
     * @var array
     */
    protected $_storesIds = [];

    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * Rating option vote model
     *
     * @var \Magento\Review\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
     * @param mixed $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    protected $_logger;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_logger = $logger;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $connection
        );
    }

    /**
     * Define module
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Catalog\Model\Product',
            'Magento\Catalog\Model\ResourceModel\Product');
        $this->setRowIdFieldName('key_id');
       // $this->_reviewStoreTable = $this->_resource->getTableName('review_store');
        $this->_initTables();
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Adds store filter into array
     *
     * @param int|int[] $storeId
     * @return $this
     */
  /*  public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStoreId();
        }

        parent::addStoreFilter($storeId);

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }*/

    /**
     * Adds specific store id into array
     *
     * @param array $storeId
     * @return $this
     */
    /*public function setStoreFilter($storeId)
    {
        if (is_array($storeId) && isset($storeId['eq'])) {
            $storeId = array_shift($storeId);
        }

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }*/

    /**
     * Applies all store filters in one place to prevent multiple joins in select
     *
     * @param Select $select
     * @return $this
     */
    /*protected function _applyStoresFilterToSelect(Select $select = null)
    {
        $connection = $this->getConnection();
        $storesIds = $this->_storesIds;
        if (null === $select) {
            $select = $this->getSelect();
        }

        if (is_array($storesIds) && count($storesIds) == 1) {
            $storesIds = array_shift($storesIds);
        }

        if (is_array($storesIds) && !empty($storesIds)) {
            $inCond = $connection->prepareSqlCondition('store.store_id', ['in' => $storesIds]);
            $select->join(
                ['store' => $this->_reviewStoreTable],
                'rt.review_id=store.review_id AND ' . $inCond,
                []
            )->group(
                'rt.review_id'
            );
        } else {
            $select->join(
                ['store' => $this->_reviewStoreTable],
                $connection->quoteInto('rt.review_id=store.review_id AND store.store_id = ?', (int)$storesIds),
                []
            );
        }

        return $this;
    }
*/
    /**
     * Add stores data
     *
     * @return $this
     */
    /*public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }
*/
    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
  /*  public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('rdt.customer_id = ?', $customerId);
        return $this;
    }
*/
    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return $this
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()->where('cdkey.product_id = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        if($status == 1){
            $this->getSelect()->where('cdkey_oi.item_id is NULL');
        }
        else{
            $this->getSelect()->where('cdkey_oi.item_id is not NULL');
        }
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return $this
     */
   /* public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }
*/
    /**
     * Add review summary
     *
     * @return $this
     */
  /*  public function addReviewSummary()
    {
        foreach ($this->getItems() as $item) {
            $model = $this->_ratingFactory->create();
            $model->getReviewSummary($item->getReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }
*/
    /**
     * Add rote votes
     *
     * @return $this
     */
  /*  public function addRateVotes()
    {
        foreach ($this->getItems() as $item) {
            $votesCollection = $this->_voteFactory->create()->getResourceCollection()->setEntityPkFilter(
                $item->getEntityId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->load();
            $item->setRatingVotes($votesCollection);
        }
        return $this;
    }
*/
    /**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $cdkeyTable = $this->_resource->getTableName('cdkey_key');
        $cdkeyOrderItemTable = $this->_resource->getTableName('cdkey_key_order_item');

        //$this->addAttributeToSelect('name')->addAttributeToSelect('sku');

        $this->getSelect()->join(
            ['cdkey' => $cdkeyTable],
            'cdkey.product_id = e.entity_id',
            ['cdkey.key_id', 'cdkey.key']
        )->joinLeft(
            ['cdkey_oi' => $cdkeyOrderItemTable],
            'cdkey.key_id = cdkey_oi.key_id',
            ['cdkey_oi.item_id']
        );
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @param null|int|string $limit
     * @param null|int|string $offset
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
   /* public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns('rt.review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }
*/
    /**
     * Get result sorted ids
     *
     * @return array
     */
   /* public function getResultingIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->reset(Select::ORDER);
        $idsSelect->columns('rt.review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }
*/
    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('dddddddddd',true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string)$select,true));

        $select->reset(Select::COLUMNS)->columns('COUNT(cdkey.key_id)')->reset(Select::HAVING);

        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string)$select,true));
        return $select;
    }

    /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
   /* public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
            case 'cdkey.key_id':
            case 'cdkey.key':
                $this->getSelect()->order($attribute . ' ' . $dir);
            \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $this->getSelect(),true));
                break;
            case 'status':
                $this->getSelect()->order('cdkey_oi.item_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $this->getSelect(),true));
        return $this;
    }*/

    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($attribute,true));
        switch ($attribute) {
            case 'key_id':
            case 'key':
                $this->getSelect()->order('cdkey.'.$attribute . ' ' . $dir);
                //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $this->getSelect(),true));
                break;
            case 'status':
                $this->getSelect()->order('cdkey_oi.item_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r((string) $this->getSelect(),true));
        return $this;

        return parent::addAttributeToSort($attribute, $dir);
    }

    /**
     * Add attribute to filter
     *
     * @param AbstractAttribute|string $attribute
     * @param array|null $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch ($attribute) {
            case 'cdkey.key_id':
            case 'cdkey.key':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            case 'status':
                //$this->_logger->debug(print_r($attribute,true));
                //$this->_logger->debug(print_r($condition,true));
                if($condition['eq'] == 0){
                    $this->getSelect()->where('cdkey_oi.item_id is not NULL');
                }else{
                    $this->getSelect()->where('cdkey_oi.item_id is NULL');
                }
                //$this->_logger->debug(print_r((string) $this->getSelect(),true));
                break;
           /* case 'type':
                if ($condition == 1) {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new \Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql(
                            'rdt.store_id',
                            ['eq' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
                        ),
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                } elseif ($condition == 2) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', ['gt' => 0]);
                } else {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new \Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql(
                            'rdt.store_id',
                            ['neq' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
                        ),
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                }
                $this->getSelect()->where($conditionSql);
                break;
*/
            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }

    /**
     * Retrieves column values
     *
     * @param string $colName
     * @return array
     */
    public function getColumnValues($colName)
    {
        $col = [];
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    /**
     * Action after load
     *
     * @return $this
     */
 /*   protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }
*/
    /**
     * Add store data
     *
     * @return void
     */
   /* protected function _addStoreData()
    {
        $connection = $this->getConnection();
        //$this->_getConditionSql('rdt.customer_id', array('null' => null));
        $reviewsIds = $this->getColumnValues('review_id');
        $storesToReviews = [];
        if (count($reviewsIds) > 0) {
            $reviewIdCondition = $this->_getConditionSql('review_id', ['in' => $reviewsIds]);
            $storeIdCondition = $this->_getConditionSql('store_id', ['gt' => 0]);
            $select = $connection->select()->from(
                $this->_reviewStoreTable
            )->where(
                $reviewIdCondition
            )->where(
                $storeIdCondition
            );
            $result = $connection->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['review_id']])) {
                    $storesToReviews[$row['review_id']] = [];
                }
                $storesToReviews[$row['review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToReviews[$item->getReviewId()])) {
                $item->setData('stores', $storesToReviews[$item->getReviewId()]);
            } else {
                $item->setData('stores', []);
            }
        }
    }
*/
    /**
     * Redeclare parent method for store filters applying
     *
     * @return $this
     */
  /*  protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->_applyStoresFilterToSelect();

        return $this;
    }*/
}
