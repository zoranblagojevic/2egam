<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 9/8/2016
 * Time: 10:58 AM
 */

namespace Fishpig\CdKey\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Default review helper
 */
class ObserverHelper extends AbstractHelper
{
    //const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    const XML_PATH_EMAIL_ENABLED			= 'cdkey/order_email/enabled';
    const XML_PATH_EMAIL_TEMPLATE		= 'cdkey/order_email/template';
    const XML_PATH_EMAIL_IDENTITY		= 'cdkey/order_email/identity';
    const XML_PATH_EMAIL_COPY_TO		= 'cdkey/order_email/copy_to';
//$this->_scopeConfig->getValue('dev/debug/template_hints', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);


    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;
    protected $_scopeConfig;
    protected $_emailEnabled;
    protected $_emailTemplate;
    protected $_emailIdentity;
    protected $_emailCopyTo;

    protected $_emulation;
    protected $_elementContext;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\App\Emulation $emulation,
        //\Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Framework\View\Element\Context $elementContext
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        $this->_scopeConfig = $scopeConfig;
        $this->_emualtion = $emulation;
        $this->_elementContext = $elementContext;
        $this->_emailEnabled = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_emailTemplate = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_emailIdentity = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $this->_emailCopyTo = $this->_scopeConfig->getValue(self::XML_PATH_EMAIL_COPY_TO, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        parent::__construct($context);
    }

    public function test(){

    }


    public function sendOrderEmail(\Magento\Sales\Model\Order $order, $only_invoiced = true, $only_product_id = false)
    {
        if (!$this->_emailEnabled) {
            return false;
        }

        $storeId = $order->getStoreId();
        $copyTo = $this->_getEmails($this->_emailCopyTo, $storeId);

        //$appEmulation = Mage::getSingleton('core/app_emulation');
        $appEmulation = $this->_emualtion;
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation($storeId);

        try {

            //$block = Mage::getSingleton('core/layout')->createBlock('cdkey/key_email_items');
            $block = $this->_elementContext->getLayout()->createBlock('CdKey/Key/Email/Items');
            $block->setOnlyInvoiced($only_invoiced);
            $block->setOrder($order);

            if($only_product_id){
                $block->setOnlyProduct($only_product_id);
            }

            $cdkeyHtml = trim($block->toHtml());

        } catch (Exception $e) {
            $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);
            throw $e;
        }


        if (!$cdkeyHtml) {
            return false;
        }

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        //$templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
        $templateId = $this->_emailTemplate;
        $customerName = $order->getCustomerName();

        /* NEEDS TO BE FINISHED!!!
        $mailer = Mage::getModel('core/email_template_mailer');
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo($order->getCustomerEmail(), $customerName);

        if ($copyTo) {
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }

        $mailer->addEmailInfo($emailInfo);

        $mailer->setSender($this->_emailIdentity);
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        	=> $order,
                'cdkey_html' => $cdkeyHtml
            )
        );

        try {
            $mailer->send();
            return true;
        }
        catch (Exception $e) {
            Mage::logException($e);
        }
*/
        return false;
    }

    /**
     * Retrieve emails from the config, exploded by a comma
     *
     * @param string $configPath
     * @param int $storeId
     * @return array|false
     */
    protected function _getEmails($configPath, $storeId)
    {
        //$data = Mage::getStoreConfig($configPath, $storeId);
        $data = $this->_scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }
}