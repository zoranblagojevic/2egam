<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/23/2016
 * Time: 2:11 PM
 */

namespace Fishpig\CdKey\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Fishpig\CdKey\Model\Resource\CdKey\Product\CollectionFactory;
use Fishpig\CdKey\Model\Resource\CdKey\Product\Collection;
use Fishpig\CdKey\Model\CdKey;

/**
 * Class ReviewDataProvider
 *
 * @method Collection getCollection
 */
class CdKeyDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->getCollection()->addEntityFilter($this->request->getParam('current_product_id', 0));
//            ->addStoreData();

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($arrItems,true));

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $field = $filter->getField();

        if (in_array($field, ['key_id', 'key'])) {
            $filter->setField('cdkey.' . $field);
        }

        if (in_array($field, ['status'])) {
            $filter->setField($field);
        }
/*
        if ($field === 'review_created_at') {
            $filter->setField('rt.created_at');
        }
*/
        parent::addFilter($filter);
    }
}