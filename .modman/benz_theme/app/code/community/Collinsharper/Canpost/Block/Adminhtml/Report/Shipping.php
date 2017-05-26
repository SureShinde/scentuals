<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Report
 * @author      Shane Harper
 */
class Collinsharper_Canpost_Block_Adminhtml_Report_Shipping extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
//        $this->_controller = 'adminhtml_sales_shipping';
        $this->_blockGroup = 'chcanpost2module';
        $this->_controller = 'adminhtml_report_shipping';
        $this->_headerText = Mage::helper('reports')->__('Advanced Shipped Report');
        parent::__construct();
        $this->setTemplate('report/grid/container.phtml');
        $this->_removeButton('add');
        $this->addButton('filter_form_submit', array(
            'label'     => Mage::helper('reports')->__('Show Report'),
            'onclick'   => 'filterFormSubmit()'
        ));
    }

    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/shipping', array('_current' => true));
    }


}