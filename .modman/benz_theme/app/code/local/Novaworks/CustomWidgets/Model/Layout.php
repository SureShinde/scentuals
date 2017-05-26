<?php
class Novaworks_CustomWidgets_Model_Layout
{
    public function toOptionArray()
    {    

			return array(
            	array('value'=>'3_columns_1', 'label'=>Mage::helper('adminhtml')->__('3 Columns (Layout 1)')),
            	array('value'=>'3_columns_2', 'label'=>Mage::helper('adminhtml')->__('3 Columns (Layout 2)')),
            	array('value'=>'2_columns_left', 'label'=>Mage::helper('adminhtml')->__('2 Columns (Left Slide Bar)')),
            	array('value'=>'2_columns_right', 'label'=>Mage::helper('adminhtml')->__('2 Columns (Right Side Bar)'))            	
        	);
    } 
}
