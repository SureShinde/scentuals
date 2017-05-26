<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Return extends Collinsharper_Canpost_Helper_Rest_Request {
    
    
    
    public function create($shipping_address)
    {
        
        $company = $shipping_address->getCompany();
        
        if (empty($company)) {
            
            $company = $shipping_address->getFirstname().' '.$shipping_address->getLastname();
            
        }
        
     $lineTwo = '';
        if(Mage::getStoreConfig('shipping/origin/street_line2')) {

                $lineTwo = '                              <address-line-2><![CDATA['.Mage::getStoreConfig('shipping/origin/street_line2').']]></address-line-2>';
        }



        $xmlRequest = '<?xml version="1.0" encoding="UTF-8"?>
        <authorized-return xmlns="http://www.canadapost.ca/ws/authreturn">
                <service-code>DOM.EP</service-code>
                <returner>
                        <name>'.$shipping_address->getFirstname().' '.$shipping_address->getLastname().'</name>
                        <company>'.$company.'</company>
                        <domestic-address>
                                <address-line-1>'.(is_array($shipping_address->getStreet()) ? implode(", ", $shipping_address->getStreet()) : $shipping_address->getStreet() ).'</address-line-1>
                                <city>'.$shipping_address->getCity().'</city>
                                <province>'.Mage::getModel('directory/region')->load($shipping_address->getRegionId())->getCode().'</province>
                                <postal-code>'.$this->formatPostalCode($shipping_address->getPostcode()).'</postal-code>
                        </domestic-address>
                </returner>
                <receiver>
                        <name>'.Mage::getStoreConfig('general/store_information/name').'</name>
                        <company>'.Mage::getStoreConfig('general/store_information/name').'</company>
                        <domestic-address>
                                <address-line-1>'.Mage::getStoreConfig('shipping/origin/street_line1').'</address-line-1>
                                ' . $lineTwo . '
                                <city>'.Mage::getStoreConfig('shipping/origin/city').'</city>
                                <province>'.Mage::getModel('directory/region')->load(Mage::getStoreConfig('shipping/origin/region_id'))->getCode().'</province>
                                <postal-code>'.$this->formatPostalCode(Mage::getStoreConfig('shipping/origin/postcode')).'</postal-code>
                        </domestic-address>
                </receiver>
                <parcel-characteristics>
                        <weight>15</weight>
                </parcel-characteristics>
                <print-preferences>
                        <encoding>PDF</encoding>
                </print-preferences>
                <settlement-info>
                        <contract-id>'.Mage::getStoreConfig('carriers/chcanpost2module/contract').'</contract-id>
                </settlement-info>
        </authorized-return>';
        
        $url = $this->getBaseUrl().'rs/' . $this->getBehalfAccount() . '/' . Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number') . '/authorizedreturn';

        $headers = array(
            'Content-Type: application/vnd.cpc.authreturn+xml',
            'Accept: application/vnd.cpc.authreturn+xml'
        );

        return $this->send($url, $xmlRequest, false, $headers);
        
    }
    
    
    public function getLabel($url)
    {
        
        return $this->send($url, '', 1, array('Accept:application/pdf'));
        
    }
    
    
}
    
