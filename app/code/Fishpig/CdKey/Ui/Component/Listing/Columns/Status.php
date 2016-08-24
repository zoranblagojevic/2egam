<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/24/2016
 * Time: 9:48 AM
 */

namespace Fishpig\CdKey\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Fishpig\CdKey\Helper\Data as StatusSource;

/**
 * Class Status
 */
class Status extends Column implements OptionSourceInterface
{
    /**
     * @var StatusSource
     */
    protected $source;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param StatusSource $source
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        StatusSource $source,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->source = $source;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        $options = $this->source->getCdKeyStatuses();

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($dataSource['data']['items'],true));
        foreach ($dataSource['data']['items'] as &$item) {
            if(is_null($item['item_id'])){
                $item['status'] = $options[1];
            }else{
                $item['status'] = $options[0];
            }
        }

        return $dataSource;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->source->getCdKeyStatusesOptionArray();
    }
}
