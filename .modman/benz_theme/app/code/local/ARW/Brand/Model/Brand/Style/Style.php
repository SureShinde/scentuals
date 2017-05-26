<?php

?>
<?php
class ARW_Brand_Model_Brand_Style_Style
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'brand-style-1',         'label'=>Mage::helper('adminhtml')->__('Brand style 1')),
            array('value'=>'brand-style-2',         'label'=>Mage::helper('adminhtml')->__('Brand style 2'))
        );
    }

}