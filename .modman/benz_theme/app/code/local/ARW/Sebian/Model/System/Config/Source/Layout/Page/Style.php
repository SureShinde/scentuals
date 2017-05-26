<?php
?>
<?php
class ARW_Sebian_Model_System_Config_Source_Layout_Page_Style
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'0', 'label'=>Mage::helper('adminhtml')->__('Stretched')),
            array('value'=>'1', 'label'=>Mage::helper('adminhtml')->__('Boxed')),
            array('value'=>'2', 'label'=>Mage::helper('adminhtml')->__('Fluid Width'))
        );
    }

}