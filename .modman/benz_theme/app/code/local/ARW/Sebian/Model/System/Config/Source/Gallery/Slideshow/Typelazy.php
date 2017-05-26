<?php

class ARW_Sebian_Model_System_Config_Source_Gallery_Slideshow_Typelazy
{
	public function toOptionArray()
    {
		return array(
			array('value' => 'ondemand',	'label' => Mage::helper('sebian')->__('Ondemand')),
            array('value' => 'progressive',	'label' => Mage::helper('sebian')->__('Progressive')),
        );
    }
}