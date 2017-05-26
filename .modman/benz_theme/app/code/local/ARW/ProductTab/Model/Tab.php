<?php
class ARW_ProductTab_Model_Tab extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('producttab/tab');
    }
	public function getArwProductData($id){
		$product_table = Mage::getSingleton('core/resource')->getTableName('arw_producttab_product');
		$read=Mage::getSingleton('core/resource')->getConnection('core_read');
		$select=$read->select()
						->from($product_table)
						->where('arw_tab_id =?',$id)
						->limit(1);
		$product_data=$read->fetchRow($select);
		return $product_data ;
	}

	public function deleteStores($id){
		return $this->getResource()->deleteStores($id);
		
	}
	public function deleteProduct($id){
		return $this->getResource()->deleteStores($id);
		
	} 
	public function import() {
        $data = $this->getData();
        $collection = $this->getCollection()
                ->addFieldToFilter('arw_identifier', $data['arw_identifier']);
		if (count($collection)>0)
		{
            return false;
		}
		$_POST['product_type']=$data['product_type'];
		$_POST['product_sort_type']=$data['product_sort_type'];
		$_POST['current_category_type']=$data['current_category_type'];
		$_POST['category_ids']=$data['product_data'];
		$_POST['stores']=explode(',',$data['store_id']);
		//var_dump($data);
        $this->setData($data);
        $this->save();
        return true;
    }
}
