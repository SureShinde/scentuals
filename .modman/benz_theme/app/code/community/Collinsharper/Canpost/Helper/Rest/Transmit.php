<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Transmit extends Collinsharper_Canpost_Helper_Rest_Request {
    
    
    public function transmit($manifest, $store_id)
    {

        $srcCountry = Mage::getStoreConfig('shipping/origin/country_id', $store_id );
  	$srcProvince = Mage::getModel('directory/region')->loadByName(Mage::getStoreConfig('shipping/origin/region_id', $store_id ), $srcCountry)->getCode();
        if(is_numeric(Mage::getStoreConfig('shipping/origin/region_id', $store_id )) && ! $srcProvince) {
           $srcProvince = Mage::getModel('directory/region')->load(Mage::getStoreConfig('shipping/origin/region_id', $store_id ))->getCode();
        }


		



        $lineTwo = '';
	if(Mage::getStoreConfig('shipping/origin/street_line2', $store_id)) {

		$lineTwo = '                              <address-line-2><![CDATA['.Mage::getStoreConfig('shipping/origin/street_line2', $store_id).']]></address-line-2>';
	}

        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
                        <transmit-set xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.canadapost.ca/ws/manifest" >
                          <group-ids>
                            <group-id>'.$manifest->getGroupId().'</group-id>
                          </group-ids>
                          <requested-shipping-point>'.$this->formatPostalCode(Mage::getStoreConfig('shipping/origin/postcode', $store_id)).'</requested-shipping-point>
                          <detailed-manifests>true</detailed-manifests>
                          <method-of-payment>'.(Mage::getStoreConfig('carriers/chcanpost2module/has_default_credit_card') ? 'CreditCard' : 'Account').'</method-of-payment>
                          <manifest-address>
                            <manifest-company><![CDATA['.Mage::getStoreConfig('general/store_information/name', $store_id).']]></manifest-company>
                            <phone-number></phone-number>
                            <address-details>
                              <address-line-1><![CDATA['.Mage::getStoreConfig('shipping/origin/street_line1', $store_id).']]></address-line-1>
                             ' . $lineTwo . '
                              <city>'.Mage::getStoreConfig('shipping/origin/city', $store_id).'</city>
                              <prov-state>' . $srcProvince . '</prov-state>
                              <postal-zip-code>'.$this->formatPostalCode(Mage::getStoreConfig('shipping/origin/postcode', $store_id)).'</postal-zip-code>
                            </address-details>
                          </manifest-address>
                        </transmit-set>';
        
        $url = $this->getBaseUrl().'rs/'.$this->getBehalfAccount() . '/' . Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number') . '/manifest';
        
        $headers = array(
            'Content-Type: application/vnd.cpc.manifest-v2+xml',
            'Accept: application/vnd.cpc.manifest-v2+xml'
        );
        
        return $this->send($url, $xmlRequest, false, $headers);
        
    }
    
}
