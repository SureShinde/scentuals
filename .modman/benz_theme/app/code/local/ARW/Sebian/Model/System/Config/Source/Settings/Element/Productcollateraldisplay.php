<?php
class ARW_Sebian_Model_System_Config_Source_Settings_Element_Productcollateraldisplay
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'tabs', 'label'  => Mage::helper('adminhtml')->__('Tabs')),
            array('value' => 'accordion', 'label'  => Mage::helper('adminhtml')->__('Accordion'))
        );
    }
}