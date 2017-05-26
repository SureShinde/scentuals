<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Manifest_Shipment extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_manifest_shipment';
        $manifest_id = $this->getRequest()->getParam('manifest_id');
        if (!empty($manifest_id)) {
            $this->_headerText = Mage::helper('sales')->__('View Manifest');
        } else {
            $this->_headerText = Mage::helper('sales')->__('Create Manifest');
        }
        $this->_blockGroup = 'chcanpost2module';
        parent::__construct();
        $this->_removeButton('add');
        $this->_addBackButton();
    }
    
    
    protected function getBackUrl() { return $this->getUrl('*/manifest'); }
    
}