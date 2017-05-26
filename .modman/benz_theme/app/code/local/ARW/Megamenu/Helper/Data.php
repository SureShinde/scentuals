<?php

class ARW_Megamenu_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCfg($optionString)
	{
		 return Mage::getStoreConfig('megamenu/' . $optionString);
	}

}
