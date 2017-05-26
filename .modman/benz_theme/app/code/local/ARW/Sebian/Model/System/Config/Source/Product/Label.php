<?php

?>
<?php
class ARW_Sebian_Model_System_Config_Source_Product_Label
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'new', 'label' => Mage::helper('adminhtml')->__('New')),
            array('value' => 'sale', 'label' => Mage::helper('adminhtml')->__('Sale')),
        );
    }

}