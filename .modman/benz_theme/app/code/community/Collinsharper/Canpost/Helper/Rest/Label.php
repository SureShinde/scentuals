<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Label extends Collinsharper_Canpost_Helper_Rest_Request
{
  
    public function getPdf($label_data, $return_transfer = 1)
    {
        
        $group_id = md5(Mage::getBaseUrl());
        
        $headers = array(
            'Accept: application/'. ((Mage::getStoreConfig('carriers/chcanpost2module/output_format') == 'zpl') ? 'zpl' : 'pdf')
        );
        
        if (!empty($label_data)) {

            if ($return_transfer) {
            
                header('Content-type: '.$label_data['media_type']);

                header('Content-Disposition: attachment; filename="label-'.date('Y-m-d--H-i-s').'.'.((Mage::getStoreConfig('carriers/chcanpost2module/output_format') == 'zpl') ? 'zpl' : 'pdf').'"');
                
                $this->send($label_data['url'], '', 1, $headers);

                return true;
            
            } else {
                
                return $this->send($label_data['url'], '', 1, $headers);
                
            }
            
        } else {
            
            return false;
            
        }
        
    }
    
    
}
