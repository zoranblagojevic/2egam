<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 2:18 PM
 */


namespace Fishpig\CdKey\Model\Resource;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CdKey extends AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('jjj',true));
       $this->_init('cdkey_key', 'key_id');
    }
}