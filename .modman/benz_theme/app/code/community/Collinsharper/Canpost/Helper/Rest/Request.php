<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Request extends Mage_Core_Helper_Abstract {

    
    protected $error;
    
    private $force_live = false;


    public function _getTokenData()
    {

        
        if (!$this->force_live && Mage::getStoreConfig('carriers/chcanpost2module/test_mode')) {
            
            $auth =  Mage::getStoreConfig('carriers/chcanpost2module/api_test_login') . ':' . Mage::getStoreConfig('carriers/chcanpost2module/api_test_password');
            
        } else {
            
            $auth = Mage::getStoreConfig('carriers/chcanpost2module/api_login') . ':' . Mage::getStoreConfig('carriers/chcanpost2module/api_password');
            
        }

        return $auth;
        
    }

    /**
     *
     * @param string $service_url
     * @param xml string $xmlRequest
     * @param bool $return_file
     * @param array $headers
     * @param string $method
     * @return string 
     */
    public function send($service_url, $xmlRequest = '', $return_file = false, $headers = array(), $method='') {

        Mage::helper('chcanpost2module')->log("canada post service url: ".$service_url);
        
        $curl = curl_init($service_url);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        //it seems to be working without certificate
        //curl_setopt($curl, CURLOPT_CAINFO, realpath(dirname($_SERVER['SCRIPT_FILENAME'])) . '/../../../third-party/cert/cacert.pem'); // Mozilla cacerts

        if (!empty($xmlRequest) || $method == 'POST') {

            curl_setopt($curl, CURLOPT_POST, true);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $xmlRequest);
            
            Mage::helper('chcanpost2module')->log("CP Request:\n".$xmlRequest);
            
        } 
        
        if (!empty($method) && $method == 'DELETE') {
            
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
            
            curl_setopt($curl, CURLOPT_HEADER, 1);
            
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, !$return_file);

        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        curl_setopt($curl, CURLOPT_USERPWD, $this->_getTokenData());

        if (!$return_file && empty($headers)) {            
            
            if (preg_match('/manifest/', $service_url)) {
                
                $headers = array('Content-Type: application/vnd.cpc.manifest-v2+xml', 'Accept: application/vnd.cpc.manifest-v2+xml');
                
            } else if (preg_match('/postoffice/', $service_url)) {
                
                $headers = array('Accept:application/vnd.cpc.postoffice+xml');
                
            } else {

                $headers = array('Content-Type: application/vnd.cpc.ship.rate+xml', 'Accept: application/vnd.cpc.ship.rate+xml');
                
            }
            
        } 

        if ($this->force_live || !Mage::getStoreConfig('carriers/chcanpost2module/test_mode')) {

            $headers[] = 'Platform-Id: '.Mage::getStoreConfig('carriers/chcanpost2module/platform_id');            
       
        }
            
        if (!empty($headers)) {
            
            $headers[] = 'Accept-language: '.( (Mage::app()->getLocale()->getLocaleCode() == 'fr_CA') ? 'fr-CA' : 'en-CA' );

            Mage::helper('chcanpost2module')->log(print_r($headers, 1));
            
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        }
        
        if (!$return_file) {
            
            try {
            
                $response = curl_exec($curl);
            
            } catch (Exception $e) {
                
                Mage::helper('chcanpost2module')->log($e->getMessage());
                
            }

            $error = curl_error($curl);

            if (!empty($error)) {

                Mage::helper('chcanpost2module')->log(__CLASS__ . "request error: ".$error);
                
                $response = null;
                
                $this->error = 'connection_error';
                
            }
            
            Mage::helper('chcanpost2module')->log("CP Response:\n".$response);

            if (!empty($method) && $method == 'DELETE') {
                return (curl_getinfo($curl,CURLINFO_HTTP_CODE) == 204);
            }
            
            return $response;
            
        } else {

            curl_exec($curl);
            
        }
        
    }
    
    
    public function getBaseUrl()
    {
        
        if (Mage::getStoreConfig('carriers/chcanpost2module/test_mode')) {
            
            $url = Mage::getStoreConfig('carriers/chcanpost2module/api_test_url');
            
        } else {
            
            $url = Mage::getStoreConfig('carriers/chcanpost2module/api_url');
            
        }
        
        if (!preg_match('/\/$/', $url)) {
            
            $url .= '/';
            
        }
        
        return $url;
        
    }
    
    
    public function getError()
    {
        
        return $this->error;
        
    }

    
    public function formatPostalCode($code) {
        
        return strtoupper(str_replace(array(' ', '-'), '', $code));
        
    }
    
    
    public function isContract() {
            
        $contract_number = Mage::getStoreConfig('carriers/chcanpost2module/contract');
        
        return (!empty($contract_number)); 
        
    }
    
    
    public function getBehalfAccount()
    {
        
        $behalf_customer = Mage::getStoreConfig('carriers/chcanpost2module/api_customer_number');

        if (!Mage::getStoreConfig('carriers/chcanpost2module/test_mode')) {

            $behalf_customer .= '-'.Mage::getStoreConfig('carriers/chcanpost2module/platform_id');

        }
        
        return $behalf_customer;
        
    }
    
    
    public function forceLive()
    {
        
        $this->force_live = true;
        
        return $this;
        
    }
    
}
