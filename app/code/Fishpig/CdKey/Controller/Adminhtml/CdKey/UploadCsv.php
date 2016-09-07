<?php
/**
 * Created by PhpStorm.
 * User: Inchoo
 * Date: 8/26/2016
 * Time: 11:52 AM
 */
namespace Fishpig\CdKey\Controller\Adminhtml\CdKey;
//namespace Magento\Catalog\Controller\Adminhtml\Category\Image;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Upload
 */
class UploadCsv extends \Magento\Backend\App\Action
{

    private $uploaderFactory;

    protected $_filesystem;

    protected $_mediaDirectory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $storageDatabase
       // \Fishpig\CdKey\Model\CsvUploader $csvUploader
    ) {
        parent::__construct($context);
        $this->uploaderFactory = $uploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storageDatabase = $storageDatabase;
        // $this->csvUploader = $csvUploader;
        //$this->imageUploader = $imageUploader;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Fishpig_CdKey::cdkey_manage');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        //$type = $this->getRequest()->getParam('type');
        $tmpPath = 'cdkeys/csv/tmp';
        /*if ($type == 'samples') {
            $tmpPath = $this->_sample->getBaseTmpPath();
        } elseif ($type == 'links') {
            $tmpPath = $this->_link->getBaseTmpPath();
        } elseif ($type == 'link_samples') {
            $tmpPath = $this->_link->getBaseSampleTmpPath();
        }
*/
  /*      \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM!!!',true));
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($_FILES,true));
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM!!!',true));
        return true;*/
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => 'csv']);

            //$result = $this->_fileHelper->uploadFromTmp($tmpPath, $uploader);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $absoluteTmpPath = $this->_mediaDirectory->getAbsolutePath($tmpPath);
            $result = $uploader->save($absoluteTmpPath);

            if (!$result) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('File can not be saved to the destination folder.')
                );
            }

            unset($result['tmp_name'], $result['path']);

            if (isset($result['file'])) {
                $relativePath = rtrim($tmpPath, '/') . '/' . ltrim($result['file'], '/');
                $this->storageDatabase->saveFile($relativePath);
            }

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($result,true));
        //return $this;
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
        //$test = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\Debug')->backtrace(true,false,true);
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($test,true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r('TU SAM!!!',true));
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($_FILES,true));
        //file_exists($_FILES['files']['tmp_name']);

        try {
            $result = $this->csvUploader->saveFileToTmpDir('files');

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];

           //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($result,true));
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug(print_r($result,true));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

        /*try {
            $result = $this->imageUploader->saveFileToTmpDir('image');

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);*/

    }
}