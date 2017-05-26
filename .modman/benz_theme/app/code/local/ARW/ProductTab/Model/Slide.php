<?php

class ARW_ProductTab_Model_Slide extends Varien_Object
{
	public function toOptionArray()
	{	
		$cats=Mage::getModel('producttab/tab')->getCollection()->getData();
		$options=array();
	/* 	$options[]=array('value'=>0,'label'=>'');  */
		foreach($cats as $cat)
			{
				$options[]=array('value'=>$cat['arw_identifier'],'label'=>$cat['arw_name']);
			}
		return $options;
	}
}