<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Sales_Shipment_Grid extends Mage_Adminhtml_Block_Sales_Shipment_Grid
{

    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        
        $this->setMassactionIdField('entity_id');
        
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        
        $this->getMassactionBlock()->setUseSelectAll(false);

        $this->getMassactionBlock()->addItem('pdfshipments_order', array(
             'label'=> Mage::helper('sales')->__('PDF Packingslips'),
             'url'  => $this->getUrl('*/sales_shipment/pdfshipments'),
        ));

        $this->getMassactionBlock()->addItem('print_shipping_label', array(
             'label'=> Mage::helper('sales')->__('Print Shipping Labels'),
             'url'  => $this->getUrl('*/sales_order_shipment/massPrintShippingLabel'),
        ));

        $this->getMassactionBlock()->addItem('print_canadapost_label', array(
             'label'=> Mage::helper('sales')->__('Print Canada Post Labels'),
             'url'  => $this->getUrl('*/shipping/massLabel'),
             //'confirm' => Mage::helper('chcanpost2module')->__('Warning: You will be billed for any shipments you create. Once created a shipment cannot be cancelled.')
        ));

        return $this;
        
    }

}
