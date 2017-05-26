<?php

class ARW_ProductTab_Model_Content extends Varien_Object
{
		const SHOW_SLIDE	= 1;
		const SHOW_GRID		= 0;
		const SHOW_LIST		= 2;
	public function toOptionArray()
	{
		return array(
			array("value"=>self::SHOW_SLIDE,'label'=>Mage::helper('producttab')->__('Show Slide')),
			array("value"=>self::SHOW_GRID,'label'=>Mage::helper('producttab')->__('Show Grid')),
			array("value"=>self::SHOW_LIST,'label'=>Mage::helper('producttab')->__('Show List')),
		);
	}
}