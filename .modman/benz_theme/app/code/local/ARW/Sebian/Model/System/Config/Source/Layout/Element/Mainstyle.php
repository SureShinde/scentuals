<?php
class ARW_Sebian_Model_System_Config_Source_Layout_Element_MainStyle
{
    public function toOptionArray()
    {
        return array(
            array('value' => '1', 'label'  => Mage::helper('adminhtml')->__('Main skin 1')),
            array('value' => '2', 'label'  => Mage::helper('adminhtml')->__('Main skin 2'))
        );
    }
}