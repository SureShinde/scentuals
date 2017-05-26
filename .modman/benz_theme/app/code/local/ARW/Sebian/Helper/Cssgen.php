<?php

class ARW_Sebian_Helper_Cssgen extends Mage_Core_Helper_Abstract
{
	protected $_generatedCssFolder;
	protected $_generatedCssPath;
	protected $_generatedCssDir;
	
	public function __construct()
	{
		$this->_generatedCssFolder = 'arw/sebian/css/_config/';
		$this->_generatedCssPath = 'frontend/nova_benz/default/' . $this->_generatedCssFolder;
		$this->_generatedCssDir = Mage::getBaseDir('skin') . '/' . $this->_generatedCssPath;
	}
	public function getGeneratedCssDir()
    {
        return $this->_generatedCssDir;
    }
	public function getDesignFile()
	{
		return $this->_generatedCssFolder . 'design_' . Mage::app()->getStore()->getCode() . '.css';
	}
}
