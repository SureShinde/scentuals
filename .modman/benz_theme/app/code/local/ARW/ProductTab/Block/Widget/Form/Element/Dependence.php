<?php
class ARW_ProductTab_Block_Adminhtml_Widget_Form_Element_Dependence extends Mage_Adminhtml_Block_Widget_Form_Element_Dependence{
    public function addFieldDependence($fieldName, $fieldNameFrom, $refValues)
    {	
		foreach($refValues as $refValues){
			$this->_depends[$fieldName][$fieldNameFrom] = $refValues;
		}
        return $this;
    }
    protected function _toHtml()
    {
        if (!$this->_depends) {
            return '';
        }
        return '<script type="text/javascript"> new ARW.FormElementDependenceController('
        . $this->_getDependsJson()
        . ($this->_configOptions ? ', ' . Mage::helper('core')->jsonEncode($this->_configOptions) : '')
        . '); </script>';
    }
}