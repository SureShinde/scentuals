<?php
class ARW_ArexWorks_Model_System_Config_Source_Quickview_Largeimage
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'change',	'label' => Mage::helper('sweet')->__('Change Image')),
            array('value' => 'popup',	'label' => Mage::helper('sweet')->__('Popup Image')),
            array('value' => 'slider',	'label' => Mage::helper('sweet')->__('Image Slider')),
        );
    }
}