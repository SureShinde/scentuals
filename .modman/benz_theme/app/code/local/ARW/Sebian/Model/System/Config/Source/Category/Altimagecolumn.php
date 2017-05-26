<?php

?>
<?php
class ARW_Sebian_Model_System_Config_Source_Category_Altimagecolumn
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'label', 'label'=>Mage::helper('adminhtml')->__('Label')),
            array('value'=>'position', 'label'=>Mage::helper('adminhtml')->__('Sort Order'))
        );
    }

}