<?php

class ARW_ProductTab_Model_Status extends Varien_Object
{
		const STATUS_ENABLED	= 1;
		const STATUS_DISABLED	= 2;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('producttab')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('producttab')->__('Disabled')
        );
    }
	public function toOptionArray()
	{
		return array(
		array("value"=>1,'label'=>Mage::helper('producttab')->__('Yes')),
		array("value"=>0,'label'=>Mage::helper('producttab')->__('No')),
		);
	}
}