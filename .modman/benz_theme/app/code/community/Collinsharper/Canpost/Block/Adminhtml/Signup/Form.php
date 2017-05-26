<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Signup_Form extends Mage_Adminhtml_Block_Template
{

    public function getPostUrl()
    {

        // 20121206 - always use production signup URL
//        if (Mage::getStoreConfig('carriers/chcanpost2module/test_mode')) {
//
//            $url = Mage::getStoreConfig('carriers/chcanpost2module/signup_url_test');
//
//        }  else {

        $url = Mage::getStoreConfig('carriers/chcanpost2module/signup_url_prod');

        //      }

        return $url;

    }
    
    
    public function getPostData()
    {
        
        $postData = array(
            'first-name' => '',
            'last-name' => '',
            'address-line-1' => Mage::getStoreConfig('shipping/origin/street_line1'),
            'prov-state' => Mage::getModel('directory/region')->load(Mage::getStoreConfig('shipping/origin/region_id'))->getCode(),
            'postal-zip-code' => Mage::getStoreConfig('shipping/origin/postcode'),
            'country-code' => Mage::getStoreConfig('shipping/origin/country_id'),
            'email' => '',
            'city' => Mage::getStoreConfig('shipping/origin/city'),
            'commercial' => true,
//            'hasSavedCards' => true,
 //           'hasMultipleCards' => true,
  //          'hasMultipleAccounts' => true,
   //         'hasMultipleContracts' => true,
        );

        $postData['token-id'] = (string) Mage::Helper('chcanpost2module/rest_registration')->getRegistrationToken();

        if (!empty($postData['token-id'])) {

            Mage::getSingleton('core/session')->setCanadapostRegistrationToken($postData['token-id']);

		$urlPar = array();

		$store = $this->getRequest()->getParam('store', '');

		$website = $this->getRequest()->getParam('website', '');

		if (!empty($store)) {

			$urlPar['store'] = $store;

		}

		if (!empty($website)) {

			$urlPar['website'] = $website;

		}

            $postData['return-url'] = Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/signup/return', $urlPar);

            $postData['platform-id'] = Mage::getStoreConfig('carriers/chcanpost2module/platform_id');

            $postData['language'] = Mage::helper('chcanpost2module')->getLocale();
        
        } else {
            
            $postData = array();
            
        }

        return $postData;
        
    }
    
    
}


?>
