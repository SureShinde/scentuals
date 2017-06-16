<?php
/*
* @Package	Magegeeks_Checkoutdiscountcoupon
* @Author   Deepak Mankotia
* @Email	deepakmankotiacse@gmail.com
*/
class Magegeeks_Checkoutdiscountcoupon_IndexController extends Mage_Core_Controller_Front_Action
{
	
	protected function _expireAjax()
	{
		if (!$this->getOnepage()->getQuote()->hasItems()
				|| $this->getOnepage()->getQuote()->getHasError()
				|| $this->getOnepage()->getQuote()->getIsMultiShipping()) {
			$this->_ajaxRedirectResponse();
			return true;
		}
		$action = $this->getRequest()->getActionName();
		if (Mage::getSingleton('checkout/session')->getCartWasUpdated(true)
				&& !in_array($action, array('index', 'progress'))) {
			$this->_ajaxRedirectResponse();
			return true;
		}
	
		return false;
	}
	
	
	protected function _ajaxRedirectResponse()
	{
		$this->getResponse()
		->setHeader('HTTP/1.1', '403 Session Expired')
		->setHeader('Login-Required', 'true')
		->sendResponse();
		return $this;
	}
	
	/**
	 * Get shipping method step html
	 *
	 * @return string
	 */
	protected function _getShippingMethodsHtml()
	{
		$layout = $this->getLayout();
		$update = $layout->getUpdate();
		$structure_layout=$this->checkNextStep();
		$update->load('checkout_onepage_'.$structure_layout['layout']);
		$layout->generateXml();
		$layout->generateBlocks();
		$output = $layout->getOutput();
		return $output;
	}
	
	/**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
	
	/**
	 * Get one page checkout model
	 *
	 * @return Mage_Checkout_Model_Type_Onepage
	 */
	public function getOnepage()
	{
		return Mage::getSingleton('checkout/type_onepage');
	}
	
	public function submitAction()
    {
    	if ($this->_expireAjax()) {
    		return;
    	}
    	if ($this->getRequest()->isPost()) {
    		$couponCode = trim($this->getRequest()->getPost('coupon_code'));
    		$result = array();
    		
    		$this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
				->collectTotals()
				->save();
			if ($couponCode == $this->_getQuote()->getCouponCode()) {
				$result = array('error' => 0);
				$this->_getSession()->setStepData('discountcode', 'complete');
			}
			else {
				$result = array('error' => 1, 'message' => $this->__('Coupon code %s is not valid.', Mage::helper('core')->htmlEscape($couponCode)));
			}
    		
    		if(!$result['error']) {
    			$this->getOnepage()->getQuote()->getShippingAddress()->setCollectShippingRates(true);
			$next_step=$this->checkNextStep();
    			 $result['goto_section'] = $next_step['goto_section'];
                    $result['update_section'] = array(
                        'name' => $next_step['name'],
                        'html' => $this->_getShippingMethodsHtml()
                    );
    		}
		
		else {
            	$result['goto_section'] = 'discountcode';
            }
    		
    		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    	}
    }
	protected function checkNextStep()
	{

		$steps=Mage::getStoreConfig('magegeeks_checkoutdiscountcoupon/checkoutdiscountcoupon_config/checkoutdiscountcoupon_step');
	
		$next_step=array('3'=>
						array('goto_section'=>'shipping_method','name'=>'shipping-method','layout'=>'shippingmethod'),
				     '4'=>
						array('goto_section'=>'payment_method','name'=>'payment-method','layout'=>'paymentmethod'),
				     '5'=>
						array('goto_section'=>'review','name'=>'review','layout'=>'review')
				);
		
		return $next_step[$steps];


	}
	public function indexAction(){
    	$this->loadLayout()->renderLayout();
    }
}
