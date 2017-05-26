<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Headerstyle
{
    public function toOptionArray()
    {
        return array(
            //array('value' => 'style_1', 'label' => Mage::helper('adminhtml')->__('Style 1')),
            array('value' => 'style_2', 'label' => Mage::helper('adminhtml')->__('Style 2')),
            array('value' => 'style_3', 'label' => Mage::helper('adminhtml')->__('Style 3')),
            array('value' => 'style_4', 'label' => Mage::helper('adminhtml')->__('Style 4')),
            array('value' => 'style_5', 'label' => Mage::helper('adminhtml')->__('Style 5'))
        );
    }
}