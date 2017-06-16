<?php
/*
* @Package	Magegeeks_Checkoutdiscountcoupon
* @Author   Deepak Mankotia
* @Email	deepakmankotiacse@gmail.com
*/
class Magegeeks_Checkoutdiscountcoupon_Block_Checkout_Onepage_Discountcode extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function isShow(){
     		return Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_enable');
     }
}
