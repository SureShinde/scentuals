<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Report
 * @author      Shane Harper
 */

class Collinsharper_Canpost_Model_Adminhtml_Report_Shipping extends Mage_Reports_Model_Mysql4_Order_Collection
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
                'o' => $this->getTable('sales/order')),
            'main_table.order_id = o.entity_id'
        )
            ->where("o.created_at BETWEEN '".$from."' AND '".$to."'")
         //   ->where('main_table.state = \'complete\'')
            ->columns(array(
            'order_id' => '`o`.`entity_id`',
            'base_total' => '`o`.`base_grand_total`',
            'shipping_amount' => '`o`.`base_shipping_amount`',
            'shipping_cost' => '`main_table`.`shipping_cost`',
            )

        );
        // uncomment next line to get the query log:
        // Mage::helper('chcanpost2module')->log(__METHOD__ . 'SQL: '.$this->getSelect()->__toString());
        return $this;
    }

    public function setStoreIds($storeIds)
    {
        return $this;
    }



}
