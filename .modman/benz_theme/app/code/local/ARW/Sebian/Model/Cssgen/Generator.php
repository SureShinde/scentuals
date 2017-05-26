<?php
class ARW_Sebian_Model_Cssgen_Generator extends Mage_Core_Model_Abstract{
    public function __construct(){
        parent::__construct();
    }
    public function generateCss($design, $websiteCode, $storeCode){
        if ($websiteCode){
            if ($storeCode) {
                $this->_generateStoreCss($design, $storeCode);
            } else {
                $this->_generateWebsiteCss($design, $websiteCode);
            }
        }else{
            $website = Mage::app()->getWebsites(false, true);
            foreach ($website as $value => $name) {
                $this->_generateWebsiteCss($design, $value);
            }
        }
    }
    protected function _generateWebsiteCss($design, $websiteCode) {
        $website = Mage::app()->getWebsite($websiteCode);
        foreach ($website->getStoreCodes() as $site){
            $this->_generateStoreCss($design, $site);
        }
    }
    protected function _generateStoreCss($design, $storeCode){
        if (!Mage::app()->getStore($storeCode)->getIsActive()) return;
        $prefix = '_' . $storeCode;
        $filename = $design . $prefix . '.css';
        $filedefault = Mage::helper('sebian/cssgen')->getGeneratedCssDir() . $filename;
        $path = 'arw/sebian/css/' . $design . '.phtml';
        Mage::register('cssgen_store', $storeCode);
        try{
            $block = Mage::app()->getLayout()->createBlock("core/template")->setData('area', 'frontend')->setTemplate($path)->toHtml();
            if (empty($block)) {
                throw new Exception( Mage::helper('sebian')->__("Template file is empty or doesn't exist: %s", $path) );
            }
            require_once __DIR__ . '/scss.inc.php';
            $scss = new scssc();
            $scss->setFormatter("arw_scss_formatter");

            $file = new Varien_Io_File();
            $file->setAllowCreateFolders(true);
            $file->open(array( 'path' => Mage::helper('sebian/cssgen')->getGeneratedCssDir() ));
            $file->streamOpen($filedefault, 'w+');
            $file->streamLock(true);
            $file_content = $scss->compile($block);
            $file_content = preg_replace('/\.\.\//','../../../../',$file_content);
            $file->streamWrite($file_content);
            $file->streamUnlock();
            $file->streamClose();
        }catch (Exception $gener){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sebian')->__('Failed generating CSS file: %s in %s', $filename, Mage::helper('sebian/cssgen')->getGeneratedCssDir()). '<br/>Message: ' . $gener->getMessage());
            Mage::logException($gener);
        }
        Mage::unregister('cssgen_store');
    }
}