<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Shane Harper
 */
class Collinsharper_Canpost_Block_Adminhtml_Report_Shipping_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        $this->setCountTotals(true);
        $this->setCountSubTotals(true);
        parent::__construct();
        $this->setId('AdvShipping');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setSubReportSize(false);

    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()->initReport('chcanpost2module/adminhtml_report_shipping');
        return  $this;
    }


    public function getReport($from, $to) {
        if ($from == '') {
            $from = $this->getFilter('report_from');
        }
        if ($to == '') {
            $to = $this->getFilter('report_to');
        }
        $totalObj = Mage::getModel('reports/totals');
        $totals = $totalObj->countTotals($this, $from, $to);
        $this->setTotals($totals);
        $this->addGrandTotals($totals);
        return $this->getCollection()->getReport($from, $to);
    }

    protected function _prepareColumns()
    {

        $this->addColumn('order_id', array(
            'header'    => Mage::helper('sales')->__('Order Id'),
            'index'     => 'order_id',
            'sortable'  => false
        ));


//        if ($this->getCollection()->getStoreIds()) {
//            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
//        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('base_total', array(
            'header'        => Mage::helper('sales')->__('Order Total'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'base_total',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));


        $this->addColumn('shipping_amount', array(
            'header'        => Mage::helper('sales')->__('Shipping Paid'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'shipping_amount',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));


        $this->addColumn('shipping_cost', array(
            'header'        => Mage::helper('sales')->__('Shipping Cost'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'shipping_cost',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));



        $this->addExportType('*/*/exportShippingCsv', Mage::helper('adminhtml')->__('CSV'));
        $this->addExportType('*/*/exportShippingExcel', Mage::helper('adminhtml')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}


