<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/7/2016
 * Time: 10:04 AM
 */

namespace Fishpig\CdKey\Observer;

class ReserveKeyFlagObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_registry;
    protected $_ttt;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Fishpig\CdKey\Model\Resource\CdKey $ttt
    )
    {
        $this->_objectManager = $objectManager;
        $this->_registry = $registry;
        $this->_ttt = $ttt;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        echo  "Hello Testing ";
        //$test =  $observer->getEvent()->getOrder()->getIncrementId();
        //$test =  $observer->getEvent()->getItem()->getId();
        //$this->_registry->register('test',$test);

        $orderItem = $observer->getEvent()->getItem()->getProductId();
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($orderItem,true));
        $rt = $this->_ttt->isProductIdValid($orderItem);
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('$rt',true));
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($rt,true));

        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('tusam!!!!!!',true));
       // \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(get_class($test),true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r(get_class($this->_ttt),true));
        die();

        $orderItem = $observer->getEvent()->getItem();

        if (!$orderItem->getId()) {
            $this->_registry->register('_reserve_keys',true);
            //$this->_reserve_keys = true;
        } else {
            $this->_registry->register('_reserve_keys',false);
            //$this->_reserve_keys = false;
        }

        return $this;
    }

   /* public function reserveKeyFlag($observer)
    {
        $orderItem = $observer->getEvent()
            ->getItem();

        if (!$orderItem->getId()) {
            $this->_reserve_keys = true;
        } else {
            $this->_reserve_keys = false;
        }

        return $this;
    }*/
}