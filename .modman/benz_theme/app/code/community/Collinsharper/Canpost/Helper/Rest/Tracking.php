<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Tracking extends Collinsharper_Canpost_Helper_Rest_Request {
    
    
    public function getDetails($pin_number)
    {
        
        $url = $this->getBaseUrl().'vis/track/pin/'.$pin_number.'/detail';
        
        $headers = array(
            'Accept: application/vnd.cpc.track+xml'
        );
        
        return $this->send($url, null, false, $headers);
        
    }
    
}
