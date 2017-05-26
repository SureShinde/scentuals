<?php

class ARW_ProductTab_Block_Adminhtml_Producttab extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_producttab';
    $this->_blockGroup = 'producttab';
    $this->_headerText = Mage::helper('producttab')->__('Tabs Manager');
    $this->_addButtonLabel = Mage::helper('producttab')->__('New Tabs');
	$this->_addButton('save', array(
            'label'     => Mage::helper('producttab')->__('Import Tabs'),
			'onclick'   => 'import_producttab_save()', 
			'id'		=> 'import_producttab_save', 
			'class'     => 'add',
        ));
    parent::__construct();
  }
}