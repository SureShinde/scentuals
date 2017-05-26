<?php
class ARW_Sebian_Model_System_Config_Source_Category_Imageoptions
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'none', 'label'=>Mage::helper('adminhtml')->__('None')),
            array('value'=>'alt_image', 'label'=>Mage::helper('adminhtml')->__('Alternative Image')),
            array('value'=>'slideshow', 'label'=>Mage::helper('adminhtml')->__('Slideshow'))
        );
    }
}