<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 2:11 PM
 */

namespace Fishpig\CdKey\Model;

use Magento\Framework\Model\AbstractModel;

class CdKey extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init('Fishpig\CdKey\Model\Resource\CdKey');
    }
}