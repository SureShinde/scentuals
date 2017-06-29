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
class Bss_SalesQtyForCustomerGroup_Block_Adminhtml_Product_Edit_Tab_Inventory_Minqty extends Bss_SalesQtyForCustomerGroup_Block_Adminhtml_Product_Edit_Tab_Inventory_Minsales_Abstract {
	public function __construct() {
        $this->setTemplate('bss/salesqtyforcustomergroup/product/tab/inventory/minqty.phtml');
    }

    public function getLabel() {
    	return $this->__('Min Sales Qty');
    }

    public function getValues() {
        if($this->isNew()) return array();
        
        $values = unserialize($this->getStockItem()->getMinSalesQtyCustomerGroup());
        return $values;
    }

    protected function _prepareLayout() {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('salesqtyforcustomergroup')->__('Add Group Min Sales Qty'),
                'onclick' => 'return groupMinQtyControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_group_min_sale_qty_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

    public function isNew() {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    public function getStockItem() {
        return $this->getProduct()->getStockItem();
    }

    public function isConfigurable() {
        return $this->getProduct()->isConfigurable();
    }

    public function getFieldValue($field) {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field);
    }

    public function isReadonly() {
        return $this->getProduct()->getInventoryReadonly();
    }

    public function getFieldSuffix() {
        return 'product';
    }
}