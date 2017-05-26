<?php

class ARW_ProductTab_Block_Adminhtml_Widget_Form_Element_Categories extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml();
		$html.='All Store View - 0 || ';
        foreach (Mage::app()->getWebsites() as $website) {
				foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
						$html.=$store->getName() ."-".$store->getId()." || ";
					}
				}
		}
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}