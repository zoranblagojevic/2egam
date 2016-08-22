<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/19/2016
 * Time: 11:05 AM
 */

namespace Fishpig\CdKey\Block\Adminhtml\CdKey;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Fishpig_CdKey';
        $this->_controller = 'adminhtml_cdkey';
        $this->_headerText = __('Manage CD Keys');
        $this->_addButtonLabel = __('Add New CD Key');

        parent::_construct();
    }
}
