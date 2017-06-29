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
class Bss_SalesQtyForCustomerGroup_Model_CatalogInventory_Observer extends Mage_CatalogInventory_Model_Observer {
	public function saveInventoryData($observer) {
        $product = $observer->getEvent()->getProduct();

        if (is_null($product->getStockData())) {
            if ($product->getIsChangedWebsites() || $product->dataHasChangedFor('status')) {
                Mage::getSingleton('cataloginventory/stock_status')
                    ->updateStatus($product->getId());
            }
            return $this;
        }

        $item = $product->getStockItem();
        if (!$item) {
            $item = Mage::getModel('cataloginventory/stock_item');
        }

        $min = array();
        $max = array();

        if(Mage::helper('salesqtyforcustomergroup/config')->isEnabled()) {
        	//min sales qty for customer group
	        $minSalesQtyCustomerGroup = Mage::app()->getRequest()->getParam('min_sales_qty_customer_group');

	        foreach ($minSalesQtyCustomerGroup as $minItem) {
	        	if($minItem['delete'] != '1') {
	        		$min[] = $minItem;
	        	}
	        }

	        if(count($min) > 0) {
	        	$item->setMinSalesQtyCustomerGroup(serialize($min));
	        } else {
	        	if(is_null($product->getData('stock_data/use_config_min_sale_qty'))) {
	        		$item->setMinSalesQtyCustomerGroup(null);
	        	}

	        	$product->setData('stock_data/use_config_min_sale_qty', true);
	        	$item->setUseConfigMinSaleQty(1);
	        }

	        //max sales qty for customer group
	        $maxSalesQtyCustomerGroup = Mage::app()->getRequest()->getParam('max_sales_qty_customer_group');

	        foreach ($maxSalesQtyCustomerGroup as $maxItem) {
	        	if($maxItem['delete'] != '1') {
	        		$max[] = $maxItem;
	        	}
	        }

	        if(count($max) > 0) {
	        	$item->setMaxSalesQtyCustomerGroup(serialize($max));
	        } else {
	        	if(is_null($product->getData('stock_data/use_config_max_sale_qty'))) {
	        		$item->setMaxSalesQtyCustomerGroup(null);
	        	}
	        	
	        	$product->setData('stock_data/use_config_max_sale_qty', true);
	        	$item->setUseConfigMaxSaleQty(1);
	        }
        }
        
        $this->_prepareItemForSave($item, $product, $min, $max);
        $item->save();
        return $this;
    }

    protected function _prepareItemForSave($item, $product, $min = array(), $max = array()) {
        $item->addData($product->getStockData())
            ->setProduct($product)
            ->setProductId($product->getId())
            ->setStockId($item->getStockId());
        if (!is_null($product->getData('stock_data/min_qty'))
            && is_null($product->getData('stock_data/use_config_min_qty'))) {
            $item->setData('use_config_min_qty', false);
        }

        //bss sales qty for customer group
        if(Mage::helper('salesqtyforcustomergroup/config')->isEnabled()) {
        	if (count($min) > 0
	            && is_null($product->getData('stock_data/use_config_min_sale_qty'))) {
	            $item->setData('use_config_min_sale_qty', false);
	        }

	        if (count($max) > 0
	            && is_null($product->getData('stock_data/use_config_max_sale_qty'))) {
	            $item->setData('use_config_max_sale_qty', false);
	        }
        } else {
        	if (!is_null($product->getData('stock_data/min_sale_qty'))
	            && is_null($product->getData('stock_data/use_config_min_sale_qty'))) {
	            $item->setData('use_config_min_sale_qty', false);
	        }

	        if (!is_null($product->getData('stock_data/max_sale_qty'))
	            && is_null($product->getData('stock_data/use_config_max_sale_qty'))) {
	            $item->setData('use_config_max_sale_qty', false);
	        }
        }   
        //end bss sales qty for customer group

        if (!is_null($product->getData('stock_data/backorders'))
            && is_null($product->getData('stock_data/use_config_backorders'))) {
            $item->setData('use_config_backorders', false);
        }
        if (!is_null($product->getData('stock_data/notify_stock_qty'))
            && is_null($product->getData('stock_data/use_config_notify_stock_qty'))) {
            $item->setData('use_config_notify_stock_qty', false);
        }
        $originalQty = $product->getData('stock_data/original_inventory_qty');
        if (strlen($originalQty)>0) {
            $item->setQtyCorrection($item->getQty()-$originalQty);
        }
        if (!is_null($product->getData('stock_data/enable_qty_increments'))
            && is_null($product->getData('stock_data/use_config_enable_qty_inc'))) {
            $item->setData('use_config_enable_qty_inc', false);
        }
        if (!is_null($product->getData('stock_data/qty_increments'))
            && is_null($product->getData('stock_data/use_config_qty_increments'))) {
            $item->setData('use_config_qty_increments', false);
        }
        return $this;
    }

    public function validateSalesQty() {
        if(Mage::helper('salesqtyforcustomergroup/config')->isEnabled()) {
            if(!Mage::app()->getRequest()->getParam('min_sales_qty_customer_group') && !Mage::app()->getRequest()->getParam('max_sales_qty_customer_group')) return;
            $helper = Mage::helper('salesqtyforcustomergroup');

            $min = array();
            $max = array();

            //min sales qty for customer group
            $minSalesQtyCustomerGroup = Mage::app()->getRequest()->getParam('min_sales_qty_customer_group');

            foreach ($minSalesQtyCustomerGroup as $minItem) {
                if($minItem['delete'] != '1') {
                    $min[] = $minItem;
                }
            }

            $minMaxCheck = array();
            if(count($min) > 0) {
                $minCheck = array();

                foreach ($min as $item) {
                    if(isset($minCheck[$item['cust_group']])) {
                        Mage::throwException($helper->__('Duplicate min sale qty customer group.'));
                    } else {
                        $minCheck[$item['cust_group']] = 1;
                        $minMaxCheck[$item['cust_group']]['min'] = $item['qty'];
                    } 
                }
            }

            //max sales qty for customer group
            $maxSalesQtyCustomerGroup = Mage::app()->getRequest()->getParam('max_sales_qty_customer_group');

            foreach ($maxSalesQtyCustomerGroup as $maxItem) {
                if($maxItem['delete'] != '1') {
                    $max[] = $maxItem;
                }
            }

            if(count($max) > 0) {
                $maxCheck = array();

                foreach ($max as $item) {
                    if(isset($maxCheck[$item['cust_group']])) {
                        Mage::throwException($helper->__('Duplicate max sale qty customer group.'));
                    } else {
                        $maxCheck[$item['cust_group']] = 1;
                        $minMaxCheck[$item['cust_group']]['max'] = $item['qty'];
                    } 
                }
            }

            foreach ($minMaxCheck as $minMax) {
                if($minMax['min'] > $minMax['max']) {
                    Mage::throwException($helper->__('"Min sale qty customer group" greater than "Max sale qty customer group"'));
                }
            }
        }
    }
}