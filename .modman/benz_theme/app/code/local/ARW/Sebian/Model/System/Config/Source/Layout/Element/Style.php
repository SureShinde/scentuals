<?php
class ARW_Sebian_Model_System_Config_Source_Layout_Element_Style
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'boxed', 'label' => Mage::helper('adminhtml')->__('Boxed')),
            array('value' => 'stretched', 'label' => Mage::helper('adminhtml')->__('Stretched')),
            array('value' => 'fluid', 'label' => Mage::helper('adminhtml')->__('Fluid Width')),
        );
    }

}