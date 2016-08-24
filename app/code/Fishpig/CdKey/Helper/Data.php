<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/24/2016
 * Time: 9:20 AM
 */

// @codingStandardsIgnoreFile

namespace Fishpig\CdKey\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Default review helper
 */
class Data extends AbstractHelper
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

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

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Filter\FilterManager $filter
    ) {
        $this->_escaper = $escaper;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Get review detail
     *
     * @param string $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br($this->filter->truncate($origDetail, ['length' => 50]));
    }

    /**
     * Return short detail info in HTML
     *
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br($this->filter->truncate($this->_escaper->escapeHtml($origDetail), ['length' => 50]));
    }

    /**
     * Return an indicator of whether or not guest is allowed to write
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsGuestAllowToWrite()
    {
        return $this->scopeConfig->isSetFlag(self::XML_REVIEW_GUETS_ALLOW, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    public function getCdKeyStatuses()
    {
        return [
            \Fishpig\CdKey\Model\CdKey::STATUS_AVAILABLE => __('Available'),
            \Fishpig\CdKey\Model\CdKey::STATUS_NOT_AVAILABLE => __('Not Available')
        ];
    }

    public function getCdKeyStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getCdKeyStatuses() as $value => $label) {
            $result[] = ['value' => $value, 'label' => $label];
        }

        return $result;
    }
}
