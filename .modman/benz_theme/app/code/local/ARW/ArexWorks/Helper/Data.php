<?php

class ARW_ArexWorks_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getSliderId($optionString)
    {
        return $this->getCfg('header/sliderev');
    }
}
