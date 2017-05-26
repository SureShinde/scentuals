<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Manifest extends Collinsharper_Canpost_Helper_Rest_Request {
    
    
    public function getManifest($url, $mediaType = '')
    {
        if (!$mediaType) {
            $mediaType = 'application/vnd.cpc.manifest-v2+xml';
        }
        
        $headers = array(
            'Content-Type: ' . $mediaType,
            'Accept: ' . $mediaType
        );
        
        return $this->send($url, '', false, $headers);
        
    }
    
    
    public function getPdf($url, $return_transfer = 1)
    {
        
        if (!empty($url)) {
            
            $headers = array(
                'Accept: application/pdf'
            );

            if ($return_transfer) {
            
                header('Content-type: application/pdf');

                header('Content-Disposition: attachment; filename="manifest-'.date('Y-m-d--H-i-s').'.pdf"');

                $this->send($url, '', 1, $headers);

                return true;
            
            } else {
     
                return $this->send($url, '', 1, $headers);
                
            }
            
        } else {
            
            return false;
            
        }
        
    }
    
    
}
