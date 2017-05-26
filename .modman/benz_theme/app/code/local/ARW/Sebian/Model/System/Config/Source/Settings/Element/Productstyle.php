<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Productstyle
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'style_1', 'label' => Mage::helper('adminhtml')->__('Style 1')),
            array('value' => 'style_2', 'label' => Mage::helper('adminhtml')->__('Style 2'))
        );
    }
}