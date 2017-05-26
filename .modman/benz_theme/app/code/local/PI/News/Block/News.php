<?php
class PI_News_Block_News extends Mage_Core_Block_Template
{
	
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getNews()     
     { 
        if (!$this->hasData('news')) {
            $this->setData('news', Mage::registry('news'));
        }
        return $this->getData('news');
        
    }
	
}
