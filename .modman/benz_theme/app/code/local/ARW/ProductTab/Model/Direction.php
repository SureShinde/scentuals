<?php

class ARW_ProductTab_Model_Direction extends Varien_Object
{
	public function toOptionArray()
	{
		return array(
		array("value"=>0,'label'=>Mage::helper('producttab')->__('Vertical')),
		array("value"=>1,'label'=>Mage::helper('producttab')->__('Horizontal')),
		);
	}
}