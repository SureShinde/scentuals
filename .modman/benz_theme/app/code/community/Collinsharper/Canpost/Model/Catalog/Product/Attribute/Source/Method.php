<?php

class Collinsharper_Canpost_Model_Catalog_Product_Attribute_Source_Method extends Varien_Object
{
    protected $_options;

    public function getAllOptions()
    {
        if (!isset($this->_options)) {
            $this->_options = Mage::getSingleton('chcanpost2module/source_method')->toOptionArray();
        }
        return $this->_options;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
