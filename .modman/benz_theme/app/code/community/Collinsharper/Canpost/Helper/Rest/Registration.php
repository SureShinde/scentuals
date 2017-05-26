<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Registration extends Collinsharper_Canpost_Helper_Rest_Request {



    public function getBaseUrl()
    {
        $url = Mage::getStoreConfig('carriers/chcanpost2module/api_url');

        if (!preg_match('/\/$/', $url))
        {
            $url .= '/';
        }
        return $url;
    }


    public function _getTokenData()
    {

        $auth = Mage::getStoreConfig('carriers/chcanpost2module/platform_api_login') . ':' . Mage::getStoreConfig('carriers/chcanpost2module/platform_api_password');
        //$auth = Mage::getStoreConfig('carriers/chcanpost2module/api_login') . ':' . Mage::getStoreConfig('carriers/chcanpost2module/api_password');
        return $auth;
    }

    public function getRegistrationToken() {
        
        $url = $this->forceLive()->getBaseUrl().'ot/token';

        $headers = array(
            'Accept: application/vnd.cpc.registration+xml',
            'Content-Type: application/vnd.cpc.registration+xml'
        );
        $response = $this->send($url, '', false, $headers, 'POST');

        $xml = new SimpleXMLElement($response);   
        
        $token = false;
        
        if (!empty($xml->{'token-id'})) {
            
            $token = $xml->{'token-id'};
            
        } else if (!empty($xml->message->description)) {
            
            $this->error = $xml->message->description;
            
        }
        
        return $token;
        
    }
    

    public function getRegistrationData($token) {
        
        $url = $this->forceLive()->getBaseUrl().'ot/token/'.$token;

        $headers = array(
            'Accept: application/vnd.cpc.registration+xml',
            'Content-Type: application/vnd.cpc.registration+xml'
        );
        $response = $this->send($url, '', false, $headers);
        
        $xml = new SimpleXMLElement($response);   
        
        if (!empty($xml->message->description)) {            
            
            $this->error = $xml->message->description;
            
            $xml = false;
            
        }
        
        return $xml;

    }
    

}