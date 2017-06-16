<?php
/*
* @Package	Magegeeks_Checkoutdiscountcoupon
* @Author   Deepak Mankotia
* @Email	deepakmankotiacse@gmail.com
*/
class Magegeeks_Checkoutdiscountcoupon_Block_Checkout_Onepage_Progress extends Mage_Checkout_Block_Onepage_Progress
{
	
	protected function _getStepCodes()
	{
		$default_setting=array('login', 'billing', 'shipping', 'shipping_method', 'payment', 'review');
		$discountcode=array('discountcode');

		$steps=array_splice( $default_setting, Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_step'), 0, $discountcode );

		$value=Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_enable')?$steps:parent::_getStepCodes();
		
		return $value;
	}
	
	
	public function isDiscountcodeStepComplete()
	{
		$stepsRevertIndex = array_flip($this->_getStepCodes());
	
		$toStep = $this->getRequest()->getParam('toStep');
	
		if ($stepsRevertIndex['discountcode'] >= $stepsRevertIndex[$toStep]) {
			return false;
		}
	
		return true;
	}
	
}
