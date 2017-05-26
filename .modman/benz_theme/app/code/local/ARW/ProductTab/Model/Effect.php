<?php

class ARW_ProductTab_Model_Effect extends Varien_Object
{
	public function toOptionArray()
	{
		return array(
		array("value"=>"show",'label'=>Mage::helper('producttab')->__('Show/Hide')),
		array("value"=>"slide",'label'=>Mage::helper('producttab')->__('Slide')),
		array("value"=>"fade",'label'=>Mage::helper('producttab')->__('Fade')),
		);
	}
}