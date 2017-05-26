<?php
?>
<?php
class ARW_Sebian_Model_System_Config_Source_Layout_Footer_Style
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('Style 1')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('Style 2'))
        );
    }

}