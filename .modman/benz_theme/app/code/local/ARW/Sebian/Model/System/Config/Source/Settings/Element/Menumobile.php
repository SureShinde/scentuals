<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Menumobile
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'accordion', 'label' => Mage::helper('adminhtml')->__('Accordion Menu')),
            array('value' => 'sidebar', 'label' => Mage::helper('adminhtml')->__('Side Menu'))
        );
    }
}