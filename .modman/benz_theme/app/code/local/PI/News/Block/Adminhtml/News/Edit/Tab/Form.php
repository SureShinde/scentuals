<?php

class PI_News_Block_Adminhtml_News_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('news_form', array('legend'=>Mage::helper('news')->__('News information')));
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('news')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
			
	  $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('news')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
	 
	  $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
	  
        $fieldset->addField('datefrom', 'date', array(
            'name'   => 'datefrom',
            'label'  => Mage::helper('news')->__('Date From'),
            'title'  => Mage::helper('news')->__('Date From'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
			'required'  => true
        ));
        $fieldset->addField('dateto', 'date', array(
            'name'   => 'dateto',
            'label'  => Mage::helper('news')->__('Date To'),
            'title'  => Mage::helper('news')->__('Date To'),
            'image'  => $this->getSkinUrl('images/grid-cal.gif'),
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'format'       => $dateFormatIso,
			'required'  => true
        ));
	
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('news')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('news')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('news')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('texto', 'editor', array(
          'name'      => 'texto',
          'label'     => Mage::helper('news')->__('Content'),
          'title'     => Mage::helper('news')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
    
      if ( Mage::getSingleton('adminhtml/session')->getNewsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getNewsData());
          Mage::getSingleton('adminhtml/session')->setNewsData(null);
      } elseif ( Mage::registry('news_data') ) {
          $form->setValues(Mage::registry('news_data')->getData());
      }
	
      return parent::_prepareForm();
  }
}
