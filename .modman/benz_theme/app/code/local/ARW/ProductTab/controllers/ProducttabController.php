<?php
class ARW_ProductTab_ProducttabController extends Mage_Core_Controller_Front_Action
{
	public function productAction()
	{	
		if($this->getRequest()->getPost('ajax_tab_id')){
			$_POST['ajax_tab_id']=$this->getRequest()->getPost('ajax_tab_id');
            $_layout    = Mage::app()->getLayout()
                ->createBlock('producttab/producttab','producttab.second')
                ->setTemplate('arw/producttab/product.phtml');
			$result = array(
				'productlist'   =>  $_layout->toHtml()
			);
		 }
       	$this->getResponse()->setBody(Zend_Json::encode($result));
	}
}
