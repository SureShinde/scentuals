<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('tab_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('producttab')->__('Category Slider Information'));
  }

  protected function _beforeToHtml()
  {
		$this->addTab('form_section', array(
          'label'     => Mage::helper('producttab')->__('General'),
          'title'     => Mage::helper('producttab')->__('General'),
          'content'   => $this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tab_form')->toHtml(),
		));
		$this->addTab('slide_section', array(
          'label'     => Mage::helper('producttab')->__('Content Information'),
          'title'     => Mage::helper('producttab')->__('Content Information'),
          'content'   => $this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tab_slide')->toHtml(),
		));
		 $this->addTab('products_section', array(
			'label'     => Mage::helper('producttab')->__('Filter Products'),
			'title'     =>Mage::helper('producttab')->__('Filter Products'),
			'content'	=>$this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tab_products')->toHtml(),
		 ));
      return parent::_beforeToHtml();
  }
}