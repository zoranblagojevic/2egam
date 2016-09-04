<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/22/2016
 * Time: 12:26 PM
 */

namespace Fishpig\CdKey\Ui\DataProvider\Product\Form\Modifier;
//namespace Fishpig\CdKey\Ui\DataProvider\Product\Form\Modifier;
/*
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
*/
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form;
use Magento\Framework\UrlInterface;

/*class Test extends AbstractModifier
{
    public function modifyMeta(array $meta)
    {
        $meta['test_fieldset_name'] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'fieldset',
                        'label' => __('Label For Fieldset'),
                        'sortOrder' => 500,
                        'collapsible' => true
                    ]
                ]
            ],
            'children' => [
                'test_field_name' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'select',
                                'componentType' => 'field',
                                'options' => [
                                    ['value' => 'test_value_1', 'label' => 'Test Value 1'],
                                    ['value' => 'test_value_2', 'label' => 'Test Value 2'],
                                    ['value' => 'test_value_3', 'label' => 'Test Value 3'],
                                ],
                                'visible' => 1,
                                'required' => 1,
                                'label' => __('Label For Element')
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $meta;
    }


    public function modifyData(array $data)
    {
        return $data;
    }
}*/

class Test extends AbstractModifier
{
    const GROUP_CDKEY = 'cdkey';
    const GROUP_CONTENT = 'downloadable';
    //const DATA_SCOPE_REVIEW = 'grouped';
    const SORT_ORDER = 200;
    //const LINK_TYPE = 'associated';

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->locator->getProduct()->getId()) {
            return $meta;
        }

        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('KRASH',true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($meta,true));
        /*$uploadCdKeys2['arguments']['data']['config'] = [
            'dataScope' => 'image',
            'fileInputName' => 'image',
            'dataType' => 'string',
            'label' => 'uploadcsv',
            'visible' => 'true',
            'formElement' => 'fileUploader',
            //            'componentType' => 'field',
            'componentType' => 'fileUploader',
            'component' => 'Magento_Downloadable/js/components/file-uploader',
            'elementTmpl' => 'Magento_Downloadable/components/file-uploader',
        //    'elementTmpl' => 'ui/form/element/uploader/uploader',
            'allowedExtensions' => 'csv, png',
         // 'baseTmpPath' => 'catalog/tmp/category',
           // 'basePath' => 'catalog/category',
            'uploaderConfig' => [
              //  'url' => 'cdkey/cdkey/uploadcsv',
                'url' => 'catalog/category_image/upload',
           //     'baseTmpPath' => 'catalog/tmp/category',
            ],
            'sortOrder' => 30,
        ];
*/
        /*$uploadCdKeys['arguments']['data']['config'] = [
            'displayAsLink' => true,
            'formElement' => 'container',
            'componentType' => 'container',
            'component' => 'Magento_Ui/js/form/components/button',
            'template' => 'ui/form/components/button/container',
            'actions' => [
                [
                    'targetName' => 'product_form.product_form.upload_cdkeys',
                    'actionName' => 'toggleModal',
                ],
            ],
            'title' => __('Upload CD Keys'),
            'provider' => false,
            'additionalForGroup' => true,
            //'source' => 'product_details',
            'sortOrder' => 20,
        ];*/

        $meta[static::GROUP_CDKEY] = [
            'children' => [
                'cdkey_listing' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => true,
                                'componentType' => 'insertListing',
                                'dataScope' => 'cdkey_listing',
                                'externalProvider' => 'cdkey_listing.cdkey_listing_data_source',
                                'selectionsProvider' => 'cdkey_listing.cdkey_listing.product_columns.ids',
                                'ns' => 'cdkey_listing',
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => false,
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id'
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id'
                                ],
                            ],
                        ],
                    ],
                ],
                //'upload_cdkeys' => $uploadCdKeys,
                //'test' => $uploadCdKeys2,
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('CD Keys'),
                        'collapsible' => true,
                        'opened' => false,
                        'componentType' => Form\Fieldset::NAME,
                        'sortOrder' =>
                            $this->getNextGroupSortOrder(
                                $meta,
                                static::GROUP_CONTENT,
                                static::SORT_ORDER
                            ),
                    ],
                ],
            ],
        ];

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productId = $this->locator->getProduct()->getId();

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;

        return $data;
    }
}