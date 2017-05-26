<?php
class ARW_Filter_Helper_Data extends Mage_Core_Helper_Abstract{
    public function getConfig($path=null){
        if ($path) return Mage::getStoreConfig($path);
        else{
            $module = Mage::app()->getRequest()->getModuleName();
            $bar    = $this->getConfig('arwfilter/general/bar');
            return Mage::helper('core')->jsonEncode(
                array(
                    'mainDOM'   => trim($this->getConfig("arwfilter/{$module}/main_selector")),
                    'layerDOM'  => trim($this->getConfig("arwfilter/{$module}/layer_selector")),
                    'enable'    => (bool)$this->getConfig("arwfilter/{$module}/enable"),
                    'bar'       => (bool)$bar
                )
            );
        }
    }

    public function isPriceEnable(){
        $module = Mage::app()->getRequest()->getModuleName();
        return $this->getConfig("arwfilter/{$module}/price");
    }
}