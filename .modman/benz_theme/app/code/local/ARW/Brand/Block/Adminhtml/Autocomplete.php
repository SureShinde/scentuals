<?php
class ARW_Brand_Block_Adminhtml_Autocomplete extends Mage_Core_Block_Abstract
{
    protected $_suggestData = null;

    protected function _toHtml()
    {
		$suggestData=$this->getSuggestData();
        $html = '';
		$count=count($suggestData);
        $count--;
		$index =0;
        $html = '<ul><li style="display:none"></li>';
        foreach ($suggestData as $key => $item) {
		$class='';
            if ($index == 0) {
                $class= ' first';
            }

            if ($index == $count) {
                $class = ' last';
            }
			$index++;
            $html .=  '<li title="'.$this->escapeHtml($item['attribute_code']).'" class="'.$class.'">'
                . '<span class="amount">'.$item['num_of_results'].'</span>'.$this->escapeHtml($item['attribute_code']).'</li>';
			
        }

        $html.= '</ul>';

        return $html;
    }

    public function getSuggestData()
    {	
		if($attributecode=$this->getRequest()->getParam('arw_attributecode')){
			$_product = Mage::getModel('catalog/product');
			$_attributes = Mage::getResourceModel('eav/entity_attribute_collection')
							->addFieldToFilter('attribute_code',array('like'=>'%'.$attributecode.'%'))
							->setEntityTypeFilter($_product->getResource()->getTypeId());
				return $_attributes;
			}
		return '';
    }
}
