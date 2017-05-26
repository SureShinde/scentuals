<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('producttab_form', array('legend'=>Mage::helper('producttab')->__('Tabs information')));
		
		$fieldset->addField('arw_name', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Name'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'arw_name',
		));
		$fieldset->addField('arw_identifier', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Identifier'),
		  'class'     => 'required-entry',
		  'required'  => true,
		  'name'      => 'arw_identifier',
		));
		 $status=Mage::getModel('producttab/status')->toOptionArray();
		$fieldset->addField('store_id','multiselect',array(
		'name'      => 'stores[]',
		'label'     => Mage::helper('producttab')->__('Store View'),
		'title'     => Mage::helper('producttab')->__('Store View'),
		'required'  => true,
		'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true)
		));
      if ( Mage::getSingleton('adminhtml/session')->getTabData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTabData());
          Mage::getSingleton('adminhtml/session')->setTabData(null);
      } elseif ( Mage::registry('tab_data') ) {
          $form->setValues(Mage::registry('tab_data')->getData());
      }
      return parent::_prepareForm();
  }
}