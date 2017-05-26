<?php
class ARW_ProductTab_Model_Mysql4_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('producttab/product');
    }
	public function addProductFilter($tab)
    {
        $this->getSelect()->where('arw_tab_id = ?', $tab);
        return $this;
    }
}