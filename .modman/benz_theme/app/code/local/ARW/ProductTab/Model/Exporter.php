<?php

class ARW_ProductTab_Model_Exporter extends Varien_Object
{
	var $_fieldstr = 'arw_name,arw_identifier,store_id,arw_use_default,arw_enable_scroll,arw_auto_play,arw_animation_loop,arw_enable_navigation,arw_margin,arw_enable_dots,arw_speed,arw_lazy_loading,arw_responsive,arw_limit,arw_row,arw_column,product_type,product_sort_type,current_category_type,product_data';
	
	public function exportTabs()
	{
		$tabs = Mage::getModel('producttab/tab')->getCollection()->getData();
		if(!count($tabs))
			return false;
			
		foreach($tabs as $tab)
		{
			$data[] = $this->getStandData($tab);
		}
	//	var_dump($data);die();
		$csv = '';
		$csv .= $this->_fieldstr ."\n";

		foreach($data as $row)
		{
			$rowstr = implode('","',$row);
			$rowstr = '"'.$rowstr.'"';
			$csv .= $rowstr."\n";
		}
		
		return $csv;
	}
	
	public function getXmlTabs()
	{
		$tabs = Mage::getModel('producttab/tab')->getCollection();
		if(!count($tabs))
			return false;
		$tabM = Mage::getModel('producttab/tab');
		foreach($tabs as $tab)
		{
			$data = $this->getStandDataXML($tab);
			$tab->setData($data);
			$tabcollection[] = $tab;
		}
		//var_dump($tabM);
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml.= '<items>';
        foreach ($tabcollection as $item) {
            $xml.= $item->toXml();
        }
        $xml.= '</items>';	
		
		return $xml;
	}
	public function getStandDataXML($tab)
	{
		$qd = $tab->getData('arw_tab_id');
		$TabL=Mage::getModel('producttab/tab')->load($qd);
		$data = $TabL->getData();
		$StoreIdStr=implode(",",$data['store_id']);
		$data['store_id']=$StoreIdStr;
		$fields = $this->_getFields();
		
		$export_data = array();
		foreach($fields as $field)
		{
			$value = isset($data[$field]) ? $data[$field] : '';
			$export_data[$field] = $value;
		}
		
		return $export_data;
	}
	public function getStandData($tab)
	{
		$TabL=Mage::getModel('producttab/tab')->load($tab['arw_tab_id']);
		$data = $TabL->getData();
		$StoreIdStr=implode(",",$data['store_id']);
		$data['store_id']=$StoreIdStr;
		$fields = $this->_getFields();
		
		$export_data = array();
		foreach($fields as $field)
		{
			$value = isset($data[$field]) ? $data[$field] : '';
			$export_data[$field] = $value;
		}
		
		return $export_data;
	}
	
	protected function _getFields()
	{
		if(! $this->getData('fields'))
		{
			$fields = explode(',',$this->_fieldstr);
			$this->setData('fields',$fields);
		}
		
		return $this->getData('fields');
	}
}