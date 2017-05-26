<?php

class ARW_Sebian_Model_System_Config_Source_Css_Background_Position
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'left_top',	'label' => Mage::helper('adminhtml')->__('Left Top')),
            array('value' => 'left_center',	'label' => Mage::helper('adminhtml')->__('Left Center')),
            array('value' => 'left_bottom',	'label' => Mage::helper('adminhtml')->__('Left Bottom')),
            array('value' => 'right_top',	'label' => Mage::helper('adminhtml')->__('Right Top')),
            array('value' => 'right_center',	'label' => Mage::helper('adminhtml')->__('Right Center')),
            array('value' => 'right_bottom',	'label' => Mage::helper('adminhtml')->__('Right Bottom')),
            array('value' => 'center_top',	'label' => Mage::helper('adminhtml')->__('Center Top')),
            array('value' => 'center_center',	'label' => Mage::helper('adminhtml')->__('Center Center')),
            array('value' => 'center_bottom',	'label' => Mage::helper('adminhtml')->__('Center Bottom')),
            array('value' => 'custom',	'label' => Mage::helper('adminhtml')->__('Custom'))
        );
    }
}