<?php
class ARW_Sebian_Model_System_Config_Source_Layout_Element_Width
{
    public function toOptionArray()
    {
        return array(
            array('value' => '960', 'label'  => Mage::helper('adminhtml')->__('960 px')),
            array('value' => '1024', 'label'  => Mage::helper('adminhtml')->__('1024 px')),
            array('value' => '1170', 'label'  => Mage::helper('adminhtml')->__('1170 px')),
            array('value' => '1200', 'label'  => Mage::helper('adminhtml')->__('1200 px')),
            array('value' => 'custom', 'label'  => Mage::helper('adminhtml')->__('Custom width')),
        );
    }
}