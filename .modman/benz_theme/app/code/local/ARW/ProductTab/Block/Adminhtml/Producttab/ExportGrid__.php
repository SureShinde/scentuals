<?php

class ARW_ProductTab_Block_Adminhtml_Producttab_ExportGrid extends Mage_Adminhtml_Block_Widget_Grid
{
     public function __construct()
  {
      parent::__construct();
      $this->setId('ProducttabExportGrid');
      $this->setDefaultSort('arw_tab_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }
  protected function _prepareCollection()
  {
	
	  $collection = Mage::getModel('producttab/tab')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
	  
  }
  protected function _prepareColumns()
  {
      $this->addColumn('arw_name', array(
          'header'    => Mage::helper('producttab')->__('Name'),
          'align'     =>'left',
          'index'     => 'arw_name',
      ));
	  $this->addColumn('arw_identifier', array(
          'header'    => Mage::helper('producttab')->__('Tab Identifier'),
          'align'     =>'left',
          'index'     => 'arw_identifier',
      ));
	  $this->addColumn('store_id', array(
          'header'    => Mage::helper('producttab')->__('Store View'),
          'align'     =>'left',
          'index'     => 'store_id',
		  'renderer'  => 'ARW_ProductTab_Block_Adminhtml_Producttab_Renderer_Store',
      ));
	  $this->addColumn('arw_use_default', array(
          'header'    => Mage::helper('producttab')->__('Use Default Config'),
          'align'     =>'left',
          'index'     => 'arw_use_default',
      ));$this->addColumn('arw_enable_scroll', array(
          'header'    => Mage::helper('producttab')->__('Enable Scroll(slider)'),
          'align'     =>'left',
          'index'     => 'arw_enable_scroll',
      ));
	  $this->addColumn('arw_auto_play', array(
          'header'    => Mage::helper('producttab')->__('Auto Play(slider)'),
          'align'     =>'left',
          'index'     => 'arw_auto_play',
      ));
	  $this->addColumn('arw_animation_loop', array(
          'header'    => Mage::helper('producttab')->__('Animation Loop(slider)'),
          'align'     =>'left',
          'index'     => 'arw_animation_loop',
      ));
	  $this->addColumn('arw_enable_navigation', array(
          'header'    => Mage::helper('producttab')->__('Enable Navigation(slider)'),
          'align'     =>'left',
          'index'     => 'arw_enable_navigation',
      ));
      $this->addColumn('arw_margin', array(
          'header'    => Mage::helper('producttab')->__('Margin(slider)'),
          'align'     =>'left',
          'index'     => 'arw_margin',
      ));$this->addColumn('arw_enable_dots', array(
          'header'    => Mage::helper('producttab')->__('Enable Dots(slider)'),
          'align'     =>'left',
          'index'     => 'arw_enable_dots',
      ));
	  $this->addColumn('arw_speed', array(
          'header'    => Mage::helper('producttab')->__('Speed(slider)'),
          'align'     =>'left',
          'index'     => 'arw_speed',
      ));
	  $this->addColumn('arw_lazy_loading', array(
          'header'    => Mage::helper('producttab')->__('Lazy Loading(slider)'),
          'align'     =>'left',
          'index'     => 'arw_lazy_loading',
      ));
	  $this->addColumn('arw_responsive', array(
          'header'    => Mage::helper('producttab')->__('Responsive(slider)'),
          'align'     =>'left',
          'index'     => 'arw_responsive',
      ));
	  $this->addColumn('arw_limit', array(
          'header'    => Mage::helper('producttab')->__('Limit'),
          'align'     =>'left',
          'index'     => 'arw_limit',
      ));
	  $this->addColumn('arw_row', array(
          'header'    => Mage::helper('producttab')->__('Row'),
          'align'     =>'left',
          'index'     => 'arw_row',
      ));
	  $this->addColumn('arw_column', array(
          'header'    => Mage::helper('producttab')->__('Column'),
          'align'     =>'left',
          'index'     => 'arw_column',
      ));
	  $this->addColumn('product_type', array(
          'header'    => Mage::helper('producttab')->__('Product Type'),
          'align'     =>'left',
          'index'     => 'product_type',
      ));
	  $this->addColumn('product_sort_type', array(
          'header'    => Mage::helper('producttab')->__('product_sort_type'),
          'align'     =>'left',
          'index'     => 'product_sort_type',
      ));
	  $this->addColumn('current_category_type', array(
          'header'    => Mage::helper('producttab')->__('Type of product in current category'),
          'align'     =>'left',
          'index'     => 'current_category_type',
      ));
	    $this->addColumn('product_data', array(
          'header'    => Mage::helper('producttab')->__('Product Data'),
          'align'     =>'left',
          'index'     => 'product_data',
      ));
		$this->addExportType('*/*/exportCsv', Mage::helper('producttab')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('producttab')->__('XML'));
      return parent::_prepareColumns();
  }
  protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }
}