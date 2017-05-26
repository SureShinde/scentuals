<?php
?>
<?php

class ARW_Sebian_Model_System_Config_Source_Css_Background_Positiony
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'top',		'label' => Mage::helper('adminhtml')->__('top')),
            array('value' => 'center',	'label' => Mage::helper('adminhtml')->__('center')),
            array('value' => 'bottom',	'label' => Mage::helper('adminhtml')->__('bottom'))
        );
    }
}