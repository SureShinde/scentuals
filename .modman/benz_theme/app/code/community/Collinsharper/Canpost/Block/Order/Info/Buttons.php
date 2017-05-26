<?php

/**
 * Block of links in Order view page
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Block_Order_Info_Buttons extends Mage_Sales_Block_Order_Info_Buttons
{
    
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('collinsharper/canpost/buttons.phtml');
    }
    
    
    public function getReturns($order)
    {
        
        return Mage::getModel('chcanpost2module/return')->getCollection()->addFieldToFilter('order_id', $order->getId());
        
    }
    
    
    public function getCpReturnUrl($order) {
        
        return $this->getUrl('canpost/shipping/getReturnLabel', array('order_id' => $order->getId()));
        
    }
    
}
