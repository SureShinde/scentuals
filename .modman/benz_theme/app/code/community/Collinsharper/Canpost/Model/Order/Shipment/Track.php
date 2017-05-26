<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Order_Shipment_Track extends Mage_Sales_Model_Order_Shipment_Track
{
    
    
    /**
     * 
     * Fixed for enabled "Add Store Code to Urls" 
     * @return type 
     */
    public function getStoreId()
    {
        
        if ($this->getShipment()) {
            
            return $this->getShipment()->getOrder()->getStoreId();
            
        }
        
        return Mage::app()
                ->getWebsite()
                ->getDefaultGroup()
                ->getDefaultStoreId();
        
    }
    
}
