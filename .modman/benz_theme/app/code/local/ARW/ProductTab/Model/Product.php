<?php
class ARW_ProductTab_Model_Product extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('producttab/product');
    }

}
