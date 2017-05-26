<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Information_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'producttab';
        $this->_controller = 'adminhtml_producttab_information';
		$this->_removeButton('back');
		$this->_removeButton('reset');
		$this->_removeButton('save');
    }

    public function getHeaderText()
    {
		return Mage::helper('producttab')->__('Information Site');
    }
}