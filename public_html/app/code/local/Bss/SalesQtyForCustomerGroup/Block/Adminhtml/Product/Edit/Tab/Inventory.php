<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_SalesQtyForCustomerGroup
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/
class Bss_SalesQtyForCustomerGroup_Block_Adminhtml_Product_Edit_Tab_Inventory extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Inventory {
	public function __construct() {
        Mage_Adminhtml_Block_Widget::__construct();
        $this->setTemplate('bss/salesqtyforcustomergroup/product/tab/inventory.phtml');
    }

    public function isEnabled() {
    	return Mage::helper('salesqtyforcustomergroup/config')->isEnabled();
    }

    protected function _prepareLayout() {
    	if($this->isEnabled()) {
    		$this->setChild('bss_min_sale_qty',
                $this->getLayout()->createBlock('salesqtyforcustomergroup/adminhtml_product_edit_tab_inventory_minqty')
            );
			
			$this->setChild('bss_max_sale_qty',
                $this->getLayout()->createBlock('salesqtyforcustomergroup/adminhtml_product_edit_tab_inventory_maxqty')
            );
    	}
    }

    //For Magento version 1.6
    public function isVirtual() {
        return $this->getProduct()->getTypeInstance()->isVirtual();
    }
}