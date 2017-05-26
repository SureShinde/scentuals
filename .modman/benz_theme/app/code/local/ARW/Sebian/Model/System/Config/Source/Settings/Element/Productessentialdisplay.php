<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Productessentialdisplay
{
    public function toOptionArray()
    {
        return array(
            array('value' => '', 'label'  => Mage::helper('adminhtml')->__('1 Column')),
            array('value' => 'left', 'label'  => Mage::helper('adminhtml')->__('2 Columns Left')),
            array('value' => 'right', 'label'  => Mage::helper('adminhtml')->__('2 Columns right')),
            array('value' => 'both', 'label'  => Mage::helper('adminhtml')->__('3 Columns'))
        );
    }
}