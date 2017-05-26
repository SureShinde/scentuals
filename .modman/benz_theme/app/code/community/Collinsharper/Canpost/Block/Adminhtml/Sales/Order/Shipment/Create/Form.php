<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Sales_Order_Shipment_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Form
{

    
    public function getSaveUrl()
    {
        
        $shipping_method = $this->getShipment()->getOrder()->getShippingMethod();

        if (preg_match('/^chcanpost2module_(.+)/', $shipping_method, $matches)) {
            
            $url = $this->getUrl('*/*/saveCp', array('order_id' => $this->getShipment()->getOrderId()));
            
        } else {
            
            $url = $this->getUrl('*/*/save', array('order_id' => $this->getShipment()->getOrderId()));
            
        }
        
        return $url;
        
    }
    
}
