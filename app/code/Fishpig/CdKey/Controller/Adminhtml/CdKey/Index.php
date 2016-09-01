<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/18/2016
 * Time: 12:36 PM
 */
namespace Fishpig\CdKey\Controller\Adminhtml\CdKey;

//use Fishpig\CdKey\Model\CdKeyFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;
    //protected $testCollectionFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
        //CdKeyFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        //$this->cdkeyCollectionFactory = $cdkeyCollectionFactory;
    }

    public function execute()
    {
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM',true));

        //die;
        //$cdkeyModel = $this->testCollectionFactory->create();
        //$item = $cdkeyModel->load(1);
        //var_dump($item->getData());
        // Get news collection
        //$cdkeyCollection = $cdkeyModel->getCollection();
        // Load all data of collection
        //var_dump($cdkeyCollection->getData());
        //exit;

        //echo 'AFRO';
        //exit;
        //Call page factory to render layout and page content
        $resultPage = $this->resultPageFactory->create();

        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Fishpig_CdKey::cdkey_manage');

        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Manage CD Keys'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Fishpig'), __('Fishpig'));
        $resultPage->addBreadcrumb(__('Hello World'), __('Manage CD Keys'));

        return $resultPage;
    }

    /*
     * Check permission via ACL resource
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fishpig_CdKey::cdkey_manage');
    }
}