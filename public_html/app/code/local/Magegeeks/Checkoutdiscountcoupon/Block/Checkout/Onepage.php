<?php
/*
* @Package	Magegeeks_Checkoutdiscountcoupon
* @Author   Deepak Mankotia
* @Email	deepakmankotiacse@gmail.com
*/
class Magegeeks_Checkoutdiscountcoupon_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
	
	public function getSteps()
	{
		$steps = array();
		$stepCodes = $this->_getStepCodes();
	
		if ($this->isCustomerLoggedIn()) {
			$stepCodes = array_diff($stepCodes, array('login'));
		}
	
		foreach ($stepCodes as $step) {
			$steps[$step] = $this->getCheckout()->getStepData($step);
		}
		
		$steps['discountcode'] = array('label'=>'Promo Code','is_show'=>true);
		
		return $steps;
	}
	
	
	protected function _getStepCodes()
	{
		$default_setting=array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
		$discountcode=array('discountcode');
	
		$value=Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_enable')?	array_splice( $default_setting, Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_step'), 0, $discountcode ):$default_setting;		
		
		
		return $default_setting;
	}
}
