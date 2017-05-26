<?php 

class Collinsharper_Canpost_Block_Adminhtml_Sales_Order_Shipment_Packaging_Grid extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging_Grid {


    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('canpost/sales/order/shipment/packaging/grid.phtml');
    }


}
