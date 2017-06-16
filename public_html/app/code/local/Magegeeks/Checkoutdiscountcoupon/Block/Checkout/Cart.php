<?php
/*
* @Package	Magegeeks_Checkoutdiscountcoupon
* @Author   Deepak Mankotia
* @Email	deepakmankotiacse@gmail.com
*/
class Magegeeks_Checkoutdiscountcoupon_Block_Checkout_Cart extends Mage_Checkout_Block_Onepage
{
	protected function _prepareLayout(){
		if(Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_remove_default')){
			$this->getLayout()->getBlock('checkout.cart')->unsetChild('coupon');
		}
	}
}
