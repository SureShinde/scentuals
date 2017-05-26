<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'producttab';
        $this->_controller = 'adminhtml_producttab';
        
      /*   $this->_updateButton('save', 'label', Mage::helper('producttab')->__('Save Tab')); */
        $this->_updateButton('delete', 'label', Mage::helper('producttab')->__('Delete Tab'));
		$this->_updateButton('save', '', array(
            'label' => Mage::helper('producttab')->__('Save'),
            'onclick' => 'saveTabForm()',
            'class' => 'save',
            'sort_order' => 10
            ), 1);
		$this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('producttab')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            'sort_order' => 10
                ), -100);
		  if ($this->getRequest()->getParam('id')) {
			 $this->_addButton('duplicate', array(
				'label' => Mage::helper('producttab')->__('Duplicate'),
				'onclick' => "location.href='{$this->getUrl('*/*/duplicate', array('id' => (int) $this->getRequest()->getParam('id')))}'",
				'class' => 'duplicate',
				'sort_order' => 10
			));
}
        $this->_formScripts[] = "
			function saveTabForm() {
                applySelectedProducts('save')
            }
            function saveAndContinueEdit() {
                applySelectedProducts('saveandcontinue')
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('tab_data') && Mage::registry('tab_data')->getId() ) {
            return Mage::helper('producttab')->__("Edit Tab '%s'", $this->htmlEscape(Mage::registry('tab_data')->getArwName()));
        } else {
            return Mage::helper('producttab')->__('Add Tab');
        }
    }
}