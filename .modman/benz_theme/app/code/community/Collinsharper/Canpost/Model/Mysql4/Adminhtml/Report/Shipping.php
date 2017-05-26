<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Report
 * @author      Shane Harper
 */

class Collinsharper_Canpost_Model_Mysql4_Adminhtml_Report_Shipping extends Mage_Reports_Model_Mysql4_Order_Collection
{
    function __construct() {
        parent::__construct();
        $this->setResourceModel('sales/order_shipment');
        $this->_init('sales/order_shipment','entity_id');
    }

    public function setDateRange($from, $to) {
        $this->_reset();
        $this->getSelect()
            ->joinInner(array(
                'o' => 'sales_flat_shipment'),
            'main_table.entity_id = o.order_id'
        )
            ->where("o.created_at BETWEEN '".$from."' AND '".$to."'")
         //   ->where('main_table.state = \'complete\'')
            ->columns(array(
            'order_id' => 'main_table.increment_id',
            'base_total' => 'main_table.base_grand_total',
            'shipping_amount' => 'main_table.base_shipping_amount',
            'shipping_cost' => 'o.shipment_cost',
            )

        );
        // uncomment next line to get the query log:
        Mage::helper('chcanpost2module')->log(__METHOD__ . 'SQLA: '.$this->getSelect()->__toString());
        return $this;
    }

    public function setStoreIds($storeIds)
    {
        return $this;
    }



}
