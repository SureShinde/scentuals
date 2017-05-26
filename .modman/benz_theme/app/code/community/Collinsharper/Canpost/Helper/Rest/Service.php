<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Service extends Collinsharper_Canpost_Helper_Rest_Request
{
    
    
    /**
     *
     * @param string $service_code
     * @param string $country_code
     * @return SimpleXMLElement 
     */
    public function getInfo($service_code, $country_code)
    {
        
        // TODO: here we SHOULD be getting the URL & media type from prior GetRates or DiscoverServices calls, but those
        //  are happening all over the place and it's not valuable to go through and change them all right now.
        $url = "{$this->getBaseUrl()}rs/ship/service/{$service_code}?country={$country_code}";

        $headers = array('Accept: application/vnd.cpc.ship.rate+xml');
        
        $response = $this->send($url, '', false, $headers);
      
	$xml = false;
        try {
	    $xml = new SimpleXMLElement($response);
	} catch (Exception $e) {

	}
        
        if ($xml === false || !empty($xml->message->description)) {
            
            $this->error = isset($xml) ? $xml->message->description: 'error';
            
        } 
        
        return $xml;
        
    }
    
}
