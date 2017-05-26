<?php
class ARW_Sebian_Model_System_Config_Source_Category_Altimageeffect
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'fade', 'label'=>Mage::helper('adminhtml')->__('Fade')),
            array('value'=>'flip', 'label'=>Mage::helper('adminhtml')->__('Horizontal Flip')),
            array('value'=>'transform', 'label'=>Mage::helper('adminhtml')->__('Horizontal Transform')),
            array('value'=>'transform_skin1', 'label'=>Mage::helper('adminhtml')->__('Horizontal Transform skin 2')),
            array('value'=>'flip_vertical', 'label'=>Mage::helper('adminhtml')->__('Vertical Flip')),
            array('value'=>'transform_vertical', 'label'=>Mage::helper('adminhtml')->__('Vertical Transform')),
            array('value'=>'transform_vertical_skin1', 'label'=>Mage::helper('adminhtml')->__('Vertical Transform skin 2'))
        );
    }
}