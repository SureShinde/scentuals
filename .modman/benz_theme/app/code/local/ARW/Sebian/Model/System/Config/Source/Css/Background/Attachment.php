<?php
?>
<?php

class ARW_Sebian_Model_System_Config_Source_Css_Background_Attachment
{
    public function toOptionArray()
    {
		return array(
            array('value' => 'inherit',	'label' => Mage::helper('adminhtml')->__('Inherit')),
			array('value' => 'fixed',	'label' => Mage::helper('adminhtml')->__('Fixed')),
            array('value' => 'scroll',	'label' => Mage::helper('adminhtml')->__('Scroll'))
        );
    }
}