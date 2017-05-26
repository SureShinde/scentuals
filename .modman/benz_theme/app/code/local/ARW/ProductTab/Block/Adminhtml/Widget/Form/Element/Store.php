<?php

class ARW_ProductTab_Block_Adminhtml_Widget_Form_Element_Store extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml();
		$html.='All Store View - <span style="color:red" >0</span> || ';
        foreach (Mage::app()->getWebsites() as $website) {
				foreach ($website->getGroups() as $group) {
					$stores = $group->getStores();
					foreach ($stores as $store) {
						$html.=$store->getName() ."-"."<span style='color:red'>".$store->getId()."</span>"." || ";
					}
				}
		}
        $html .= $this->getAfterElementHtml();
        return $html;
    }
}