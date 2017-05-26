<?php
?>
<?php

class ARW_Sebian_Model_System_Config_Source_Css_Background_Positionx
{
    public function toOptionArray()
    {
		return array(
			array('value' => 'left',	'label' => Mage::helper('adminhtml')->__('left')),
            array('value' => 'center',	'label' => Mage::helper('adminhtml')->__('center')),
            array('value' => 'right',	'label' => Mage::helper('adminhtml')->__('right'))
        );
    }
}