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
abstract class Bss_SalesQtyForCustomerGroup_Block_Adminhtml_Product_Edit_Tab_Inventory_Minsales_Abstract
	extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface {

    protected $_element;
    protected $_customerGroups;

    public function getProduct() {
        return Mage::registry('product');
    }

    public function render(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->toHtml();
    }

    public function setElement(Varien_Data_Form_Element_Abstract $element) {
        $this->_element = $element;
        return $this;
    }

    public function getElement() {
        return $this->_element;
    }

    public function getCustomerGroups($groupId = null) {
        if ($this->_customerGroups === null) {
            if (!Mage::helper('catalog')->isModuleEnabled('Mage_Customer')) {
                return array();
            }
            $collection = Mage::getModel('customer/group')->getCollection();
            $this->_customerGroups = $this->_getInitialCustomerGroups();

            foreach ($collection as $item) {
                /** @var $item Mage_Customer_Model_Group */
                $this->_customerGroups[$item->getId()] = $item->getCustomerGroupCode();
            }
        }

        if ($groupId !== null) {
            return isset($this->_customerGroups[$groupId]) ? $this->_customerGroups[$groupId] : array();
        }

        return $this->_customerGroups;
    }

    protected function _getInitialCustomerGroups() {
        return array();
    }

    public function getDefaultCustomerGroup() {
        return Mage_Customer_Model_Group::CUST_GROUP_ALL;
    }

    public function getAddButtonHtml() {
        return $this->getChildHtml('add_button');
    }

    public function getSalesQtyValidation($default) {
        if ($this->hasData('price_validation')) {
            return $this->getData('price_validation');
        } else {
            return $default;
        }
    }

    public function getAttribute() {
        return $this->getElement()->getEntityAttribute();
    }

    public function isScopeGlobal() {
        return true;
    }
}