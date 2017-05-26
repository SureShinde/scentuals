<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Office extends Collinsharper_Canpost_Helper_Rest_Request
{
    
    
    public function getNearest($postal_code)
    {
    
        $list_size = Mage::getStoreConfig('carriers/chcanpost2module/postoffice_list_size');
            
        $url = $this->getBaseUrl().'rs/postoffice?d2po=true&postalCode='.urlencode($this->formatPostalCode($postal_code)).'&maximum='.$list_size;

        $headers = array(
            'Accept: application/vnd.cpc.postoffice+xml'
        );
        
        return $this->send($url, '', false, $headers);
        
    }
    
    
    public function getDetails($url)
    {
        $headers = array(
            'Accept: application/vnd.cpc.postoffice+xml'
        );
        return $this->send($url, '', false, $headers);
        
    }
    
}