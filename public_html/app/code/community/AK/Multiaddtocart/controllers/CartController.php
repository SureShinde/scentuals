<?php

require_once 'Mage/Checkout/controllers/CartController.php';

class AK_Multiaddtocart_CartController extends Mage_Checkout_CartController
{

	public function multiaddtocartAction(){
		
		$cart   = $this->_getCart();
		try{

		$productIds = $this->getRequest()->getParam('check',array());

		if (!is_array($productIds)) {
            $this->_goBack();
            return;
        }
        

        foreach ($productIds as $productId) {
        	$product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
                
            if (!$product) {
                $this->_goBack();
                return;
            }
            $qtyname = 'qty_'.$productId;
            $params['qty'] = (int) $this->getRequest()->getParam($qtyname);
            $cart->addProduct($product, $params);
            $productNames[]=$product->getName();
        }

        $cart->save();
        $this->_getSession()->setCartWasUpdated(true);
        if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml(implode(",",$productNames)));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
        }

		} catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice(Mage::helper('core')->escapeHtml($e->getMessage()));
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError(Mage::helper('core')->escapeHtml($message));
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }

	}

}