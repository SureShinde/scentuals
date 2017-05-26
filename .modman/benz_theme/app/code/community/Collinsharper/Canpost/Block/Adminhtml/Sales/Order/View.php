<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Sales_Order_View extends Mage_Adminhtml_Block_Sales_Order_View
{

    public function __construct()
    {
                
        parent::__construct();
        
        $order = $this->getOrder();
        
        $shipping_method = $order->getShippingMethod();
        
        if (preg_match('/^chcanpost2module_/', $shipping_method)) {

            $this->_removeButton('order_ship');
            
            //do this just to save order of buttons
            $this->_removeButton('order_reorder');
         
	    $shipped = 0;

	    $ordered = 0;
 
            foreach ($order->getAllItems() as $item) { 
	   
		$shipped += $item->getQtyShipped();

		$ordered += $item->getQtyOrdered();
 
	    }

            if ($this->_isAllowedAction('ship') && $order->canShip()
                && !$order->getForcedDoShipmentWithInvoice() && $ordered > $shipped) {                
                $this->_addButton('approve_order_ship', array(
                    'label'     => Mage::helper('chcanpost2module')->__('Approve Shipment'),
                    'onclick'   => 'setLocation(\'' . $this->getShipUrl() . '\')',
                    'class'     => 'go'
                ));
            }

            if ($this->_isAllowedAction('ship') && (int)Mage::getModel('chcanpost2module/shipment')->getShipmentByOrderId($order->getId())->getId() == 0 
                && !$order->getForcedDoShipmentWithInvoice() && $order->getShipmentsCollection()->getSize() > 0) {  
                $this->_addButton('cancel_order_ship', array(
                    'label'     => Mage::helper('chcanpost2module')->__('Cancel Shipment'),
                    'onclick'   => 'setLocation(\'' . $this->getCancelShipUrl() . '\')',
                    'class'     => 'go'
                ));
            }

            if ($this->_isAllowedAction('reorder')
                && $this->helper('sales/reorder')->isAllowed($order->getStore())
                && (
                        (method_exists($order,'canReorderIgnoreSalable') && $order->canReorderIgnoreSalable())
                        || (method_exists($order,'canReorder') && $order->canReorder()) //for compatability with v.1.6 of magento
                    ) 
            ) {                
                $this->_addButton('order_reorder', array(
                    'label'     => Mage::helper('sales')->__('Reorder'),
                    'onclick'   => 'setLocation(\'' . $this->getReorderUrl() . '\')',
                    'class'     => 'go'
                ));
            }
        
        }
        
    }


    public function getCancelShipUrl() {

	return $this->getUrl('*/sales_order_shipment/cancel');

    }
    
}
