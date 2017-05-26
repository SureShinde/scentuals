<?php

class PI_News_Block_Detalle extends Mage_Core_Block_Template{

	protected $_newsCollection = null;


	protected function _prepareLayout()
	{
		$this->getLayout()->createBlock('news/breadcrumbs');
		return parent::_prepareLayout();
	}

	
	public function getLoadedNewsCollection()
	{
		return $this->_getNewsCollection();
	}

	public function _getNewsCollection()
	{
		$news_id = $this->getRequest()->getParam('id');
			
  		if($news_id != null && $news_id != '')	{
			$news = Mage::getModel('news/news')->load($news_id)->getData();
		} else {
			$news = null;
		}	
		
		if($news == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$newsTable = $resource->getTableName('news');
			
			$select = $read->select()
			   ->from($newsTable,array('news_id','title','texto','status'))
			   ->where('publicar',1);
			   
			   
			$news = $read->fetchRow($select);
			
		}
						
		
		return $news;
	}
	
	
	
}
