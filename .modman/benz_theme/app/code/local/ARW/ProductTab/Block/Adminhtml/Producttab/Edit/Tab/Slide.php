<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit_Tab_Slide extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('slide_form', array('legend'=>Mage::helper('producttab')->__('Slide information')));
		$status=Mage::getModel('producttab/status')->toOptionArray();
		$default = $fieldset->addField('arw_use_default', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Use Config Default'),
		  'name'      => 'arw_use_default',
		  'values'    => $status,
		));
		$scroll = $fieldset->addField('arw_enable_scroll', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Type Show'),
		  'name'      => 'arw_enable_scroll',
		  'values'    => Mage::getModel('producttab/content')->toOptionArray(),
		));
		
		$autoplay = $fieldset->addField('arw_auto_play', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Auto Play'),
		  'name'      => 'arw_auto_play',
		  'values'    => $status,
		));
		$loop     = $fieldset->addField('arw_animation_loop', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Animation Loop'),
		  'name'      => 'arw_animation_loop',
		  'values'    => $status,
		));
		$navigation= $fieldset->addField('arw_enable_navigation', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Enable Navigation'),
		  'name'      => 'arw_enable_navigation',
		  'values'    => $status,
		));
          $margin    = $fieldset->addField('arw_margin', 'text', array(
              'label'     => Mage::helper('producttab')->__('Margin'),
              'name'      => 'arw_margin',
          ));
		$dots      = $fieldset->addField('arw_enable_dots', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Enable Dots'),
		  'name'      => 'arw_enable_dots',
		  'values'    => $status,
		));
		$speed    = $fieldset->addField('arw_speed', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Speed'),
		  'name'      => 'arw_speed',
		));
		$lazyloading = $fieldset->addField('arw_lazy_loading', 'select', array(
		  'label'     => Mage::helper('producttab')->__('Lazy Loading '),
		  'name'      => 'arw_lazy_loading',
		  'values'    => $status,
		));
		$responsive = $fieldset->addField('arw_responsive', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Responsive'),
		  'name'      => 'arw_responsive',
		  'note'		=>'Example 1170:4,950:3',
		));
		$limit   = $fieldset->addField('arw_limit', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Limit'),
		  'name'      => 'arw_limit',
		));
		$row = $fieldset->addField('arw_row', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Row'),
		  'name'      => 'arw_row',
		));
		$column=	$fieldset->addField('arw_column', 'text', array(
		  'label'     => Mage::helper('producttab')->__('Column'),
		  'name'      => 'arw_column',
		));
		
		
      if ( Mage::getSingleton('adminhtml/session')->getTabData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getTabData());
          Mage::getSingleton('adminhtml/session')->setTabData(null);
      } elseif ( Mage::registry('tab_data') ) {
          $form->setValues(Mage::registry('tab_data')->getData());
      }
	   if (version_compare(Mage::getVersion(), '1.7.0.0') < 0){
        //    $dependenceElement = $this->getLayout()->createBlock('arexmage/adminhtml_widget_form_element_dependence');
        }else{
            $dependenceElement = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        }
         $this->setChild('form_after', $dependenceElement
            ->addFieldMap($default->getHtmlId(), $default->getName())
            ->addFieldMap($scroll->getHtmlId(), $scroll->getName())
            ->addFieldMap($autoplay->getHtmlId(), $autoplay->getName())
            ->addFieldMap($loop->getHtmlId(), $loop->getName())
            ->addFieldMap($navigation->getHtmlId(), $navigation->getName())
            ->addFieldMap($margin->getHtmlId(), $margin->getName())
            ->addFieldMap($dots->getHtmlId(), $dots->getName())
            ->addFieldMap($speed->getHtmlId(), $speed->getName())
            ->addFieldMap($lazyloading->getHtmlId(), $lazyloading->getName())
            ->addFieldMap($responsive->getHtmlId(), $responsive->getName())
            ->addFieldMap($limit->getHtmlId(), $limit->getName())
            ->addFieldMap($row->getHtmlId(), $row->getName())
            ->addFieldMap($column->getHtmlId(), $column->getName())
            ->addFieldDependence($scroll->getName(), $default->getName(),0)
			->addFieldDependence($autoplay->getName(), $default->getName(),0)
			->addFieldDependence($loop->getName(), $default->getName(),0)
			->addFieldDependence($navigation->getName(), $default->getName(),0)
             ->addFieldDependence($margin->getName(), $default->getName(),0)
			->addFieldDependence($dots->getName(), $default->getName(),0)
			->addFieldDependence($speed->getName(), $default->getName(),0)
			->addFieldDependence($lazyloading->getName(), $default->getName(),0)
			->addFieldDependence($responsive->getName(), $default->getName(),0)
			->addFieldDependence($limit->getName(), $default->getName(),0)
			->addFieldDependence($row->getName(), $default->getName(),0)
			->addFieldDependence($column->getName(), $default->getName(),0)
			->addFieldDependence($column->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_GRID)
			->addFieldDependence($autoplay->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($navigation->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
             ->addFieldDependence($margin->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($loop->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($dots->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($speed->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($lazyloading->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($responsive->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
			->addFieldDependence($row->getName(), $scroll->getName(),ARW_ProductTab_Model_Content::SHOW_SLIDE)
        );
      return parent::_prepareForm();
  }
}