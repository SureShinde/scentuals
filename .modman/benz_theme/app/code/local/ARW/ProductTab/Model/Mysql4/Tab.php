<?php
class ARW_ProductTab_Model_Mysql4_Tab extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('producttab/tab', 'arw_tab_id');
    }	
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
    {
		$select_store = $this->_getReadAdapter()->select()
            ->from($this->getTable('store'))
            ->where('arw_tab_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchAll($select_store)) {
            $storesArray = array();
            foreach ($data as $row) {
                $storesArray[] = $row['store_id'];
            }
            $object->setData('store_id', $storesArray);
        }
		 $select_p = $this->_getReadAdapter()->select()
            ->from($this->getTable('product'))
            ->where('arw_tab_id = ?', $object->getId());

        if ($data = $this->_getReadAdapter()->fetchRow($select_p)) {
			if($data['product_type']==ARW_ProductTab_Model_Product_Type::NONE){
					$productString = $data['product_data'];
					$object->setData('in_products', $productString);
				 }
			$object->setData('product_type',$data['product_type']);
			$object->setData('product_sort_type', $data['product_sort_type']);
			$object->setData('current_category_type', $data['current_category_type']);
			$object->setData('product_data', $data['product_data']);
            }
        return parent::_afterLoad($object);
		
	} 
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {

		if (!$this->getIsUniqueCategory($object)) {
            Mage::throwException(Mage::helper('producttab')->__('Identifier already exist.'));
        }

        return $this;
    }
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if(!empty($product_data)){unset($product_data);}
		 $condition = $this->_getWriteAdapter()->quoteInto('arw_tab_id = ?', $object->getId());
		 $this->_getWriteAdapter()->delete($this->getTable('product'), $condition);
		 if(isset($_POST['product_type'])){
			 switch($_POST['product_type']){
				case ARW_ProductTab_Model_Product_Type::NONE:
					$product_data=Mage::helper('producttab')->prepareArray($_POST['productIds']);
					break;
				case ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY:
					$current_category_type=$_POST['current_category_type'];
					break;
				 default:
					$product_data=ltrim(str_replace('Array','',Mage::helper('producttab')->prepareArray($_POST['category_ids'])),',');		
					break; 
				}
					
			$arw_product=array(
				'arw_tab_id'=>$object->getId(),
				'product_type'=>$_POST['product_type'],
				'product_sort_type'=>$_POST['product_sort_type'],
				'product_data'	=>$product_data,
				'current_category_type'=>$current_category_type
			);
			$this->_getWriteAdapter()->insert($this->getTable('product'), $arw_product);
		 }
		if(isset($_POST['stores'])) {
			 $this->_getWriteAdapter()->delete($this->getTable('store'), $condition);
			foreach ((array)$_POST['stores'] as $store) {
				$storeArray = array();
				$storeArray['arw_tab_id'] = $object->getId();
				$storeArray['store_id'] = $store;
				$this->_getWriteAdapter()->insert($this->getTable('store'), $storeArray);
			}
		}
    
        return parent::_afterSave($object);
        
	}
	public function duplicate($id) {
        $table = Mage::getSingleton('core/resource')->getTableName('arw_producttab_tab');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $tab = $this->_getReadAdapter()->select()
            ->from($this->getTable('tab'))
            ->where('arw_tab_id = ?',$id);
		$last_id=Mage::getModel("producttab/tab")->getCollection()->getLastItem()->getArwTabId();
		$data = $this->_getReadAdapter()->fetchRow($tab); 
		$data['arw_tab_id']=$last_id+1;
		$data['arw_name']='Duplicate_'.$data['arw_name'].'_'.$data['arw_tab_id'];
		$data['arw_identifier']='duplicate_'.$data['arw_identifier'].'_'.$data['arw_tab_id'];
		$this->_getWriteAdapter()->insert($this->getTable('tab'), $data);
		$newId=$this->_getWriteAdapter()->lastInsertId();
        /* $newGroupId = $connection->lastInsertId(); */
		if(!$newId) return false;    
		$store = $this->_getReadAdapter()->select()
            ->from($this->getTable('store'))
            ->where('arw_tab_id = ?',$id);
		$stores = $this->_getReadAdapter()->fetchAll($store);
		if (count($stores)>0) {
            foreach ($stores as $store) {	
				$store_data['arw_tab_id']=$newId;
				$store_data['store_id']=$store['store_id'];
                $this->_getWriteAdapter()->insert($this->getTable('store'), $store_data);
            }
        }
        $product_con = $this->_getReadAdapter()->select()
            ->from($this->getTable('product'))
            ->where('arw_tab_id = ?',$id);
		$data_product = $this->_getReadAdapter()->fetchRow($product_con);
		$data_product['arw_tab_id']=$newId;
		$this->_getWriteAdapter()->insert($this->getTable('product'), $data_product);          
		
        return $newId; 
    }
	protected function getIsUniqueCategory($object)
	{
		$select=$this->_getWriteAdapter()->select()->from($this->getMainTable())->where($this->getMainTable().'.arw_identifier = ?', $object->getData('arw_identifier'));
		if($object->getId())
		{
			$select->where($this->getMainTable().'.arw_tab_id <> ?',$object->getId());
		}
		if ($this->_getWriteAdapter()->fetchRow($select)) {
            return false;
        }
        return true;
    } 
	public function deleteStores($id){
		$producttab_store = Mage::getSingleton('core/resource')->getTableName('arw_producttab_store');		
		$db = $this->_getWriteAdapter();
			try {
					$db->beginTransaction();
					$db->exec("DELETE FROM ".$producttab_store." WHERE arw_tab_id = $id");
					$db->commit();
					
				} catch(Exception $e) {
					$db->rollBack();
					throw new Exception($e);
				}
		}
		public function deleteProduct($id){

		$products = Mage::getSingleton('core/resource')->getTableName('arw_producttab_product');
		$db = $this->_getWriteAdapter();
		try {
				$db->beginTransaction();
				$db->exec("DELETE FROM ".$products." WHERE arw_tab_id = $id");
				$db->commit();
				
			} catch(Exception $e) {
				$db->rollBack();
				throw new Exception($e);
			}
		} 
	 
}