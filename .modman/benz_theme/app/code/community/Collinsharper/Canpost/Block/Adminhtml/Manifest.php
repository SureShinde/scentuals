<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Manifest extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    
    public function __construct()
    {
        $this->_controller = 'adminhtml_manifest';
        $this->_headerText = Mage::helper('sales')->__('Manifests');
        $this->_blockGroup = 'chcanpost2module';
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('add', array(
            'label'     => Mage::helper('chcanpost2module')->__('Create New Manifest'),
            'onclick'   => 'setLocation(\''.$this->getUrl('*/*/create').'\');',
            'class'     => 'add',
        ));
    }
    
}