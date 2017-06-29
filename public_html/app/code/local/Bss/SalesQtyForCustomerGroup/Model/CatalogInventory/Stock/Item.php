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
class Bss_SalesQtyForCustomerGroup_Model_CatalogInventory_Stock_Item extends Mage_CatalogInventory_Model_Stock_Item {
    protected $_minSaleQtyCache = array();

	public function getMinSaleQty() {
		if(!Mage::helper('salesqtyforcustomergroup/config')->isEnabled()) {
			return parent::getMinSaleQty();
		}

        $customerGroupId = $this->getCustomerGroupId();
        if (!$customerGroupId) {
            $customerGroupId = Mage::app()->getStore()->isAdmin()
                ? Mage_Customer_Model_Group::CUST_GROUP_ALL
                : Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        if (!isset($this->_minSaleQtyCache[$customerGroupId])) {
            if($this->getUseConfigMinSaleQty() == '1') {
            	$minSaleQty = Mage::helper('cataloginventory/minsaleqty')->getConfigValue($customerGroupId);
            } else {
            	$min = unserialize($this->getData('min_sales_qty_customer_group'));

            	$minSaleQty = null;
            	foreach ($min as $minItem) {
            		if($minItem['cust_group'] == $customerGroupId) {
            			$minSaleQty = $minItem['qty'];
            		}
            	}

            	if(is_null($minSaleQty)) {
            		$minSaleQty = Mage::helper('cataloginventory/minsaleqty')->getConfigValue($customerGroupId);
            	}
            }


            $this->_minSaleQtyCache[$customerGroupId] = empty($minSaleQty) ? 0 : (float)$minSaleQty;
        }

        return $this->_minSaleQtyCache[$customerGroupId] ? $this->_minSaleQtyCache[$customerGroupId] : null;
    }

    public function getMaxSaleQty() {
    	if(!Mage::helper('salesqtyforcustomergroup/config')->isEnabled()) {
			return parent::getMaxSaleQty();
		}

		$customerGroupId = $this->getCustomerGroupId();
        if (!$customerGroupId) {
            $customerGroupId = Mage::app()->getStore()->isAdmin()
                ? Mage_Customer_Model_Group::CUST_GROUP_ALL
                : Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        if($this->getUseConfigMaxSaleQty() == '1') {
        	$maxSaleQty = Mage::getStoreConfig(self::XML_PATH_MAX_SALE_QTY);
        } else {
        	$max = unserialize($this->getData('max_sales_qty_customer_group'));

        	$maxSaleQty = null;
        	foreach ($max as $maxItem) {
        		if($maxItem['cust_group'] == $customerGroupId) {
        			$maxSaleQty = $maxItem['qty'];
        		}
        	}

        	if(is_null($maxSaleQty)) {
        		$maxSaleQty = Mage::getStoreConfig(self::XML_PATH_MAX_SALE_QTY);
        	}
        }

        return (float) $maxSaleQty;
    }
}