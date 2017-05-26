<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Sidebarmobile
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'hide', 'label' => Mage::helper('adminhtml')->__('Hide')),
            array('value' => 'show', 'label' => Mage::helper('adminhtml')->__('Show in bottom')),
            array('value' => 'toggle', 'label' => Mage::helper('adminhtml')->__('Toggle'))
        );
    }
}