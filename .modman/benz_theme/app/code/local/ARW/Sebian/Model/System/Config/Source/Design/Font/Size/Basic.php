<?php
?>
<?php
class ARW_Sebian_Model_System_Config_Source_Design_Font_Size_Basic
{
    public function toOptionArray()
    {
		return array(
            array('value' => '',	    'label' => Mage::helper('adminhtml')->__('Select Font Size')),
			array('value' => '12px',	'label' => Mage::helper('adminhtml')->__('12 px')),
			array('value' => '13px',	'label' => Mage::helper('adminhtml')->__('13 px')),
            array('value' => '14px',	'label' => Mage::helper('adminhtml')->__('14 px'))
        );
    }
}