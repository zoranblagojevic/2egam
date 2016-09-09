<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/8/2016
 * Time: 1:01 PM
 */
namespace Fishpig\CdKey\Model\System\Config\Source\Catalog\Product\Attribute;

class Set
{
    public function toOptionArray()
    {
        $options = array();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attributeSetCollection = $objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection');

        $collection = $attributeSetCollection
            ->addFieldToFilter('entity_type_id', 4)
            ->load();

        foreach($collection as $set) {
            $options[] = array(
                'label' => $set->getAttributeSetName(),
                'value' => $set->getId(),
            );
        }

        return $options;
    }
}