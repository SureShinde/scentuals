<?php
class ARW_ProductTab_Model_Mysql4_Tab_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('producttab/tab');
    }
	 public function addStoreFilter($store)
    {
      if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }

        $this->getSelect()->join(
            array('store_table' => $this->getTable('store')),
            'main_table.arw_tab_id = store_table.arw_tab_id',
            array()
        )
        ->where('store_table.store_id in (?)', array(0, $store));

        return $this;
    } 
}