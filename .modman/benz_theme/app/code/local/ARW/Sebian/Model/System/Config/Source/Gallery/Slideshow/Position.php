<?php

class ARW_Sebian_Model_System_Config_Source_Gallery_Slideshow_Position
{
	public function toOptionArray()
    {
		return array(
			array('value' => 'left',	'label' => Mage::helper('sebian')->__('Left')),
            array('value' => 'right',	'label' => Mage::helper('sebian')->__('Right')),
			array('value' => 'top',	    'label' => Mage::helper('sebian')->__('Top')),
			array('value' => 'bottom',	'label' => Mage::helper('sebian')->__('Bottom')),
            array('value' => 'inside',	'label' => Mage::helper('sebian')->__('Inside')),
        );
    }
}