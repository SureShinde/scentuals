<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('producttabId');
      $this->setDefaultSort('arw_tab_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
	  $this->setUseAjax(FALSE);
  }

  protected function _prepareCollection()
  {
	
	  $collection = Mage::getModel('producttab/tab')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
	  
  }

  protected function _prepareColumns()
  {
       $this->addColumn('arw_tab_id', array(
          'header'    => Mage::helper('producttab')->__('Tab Id'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'arw_tab_id',
      ));

      $this->addColumn('arw_name', array(
          'header'    => Mage::helper('producttab')->__('Name'),
          'align'     =>'left',
          'index'     => 'arw_name',
      ));
	   $this->addColumn('arw_identifier', array(
          'header'    => Mage::helper('producttab')->__('Identifier'),
          'align'     =>'left',
          'index'     => 'arw_identifier',
      ));
	  if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        =>  Mage::helper('producttab')->__('Store View'),
                'index'         =>  'store_id',
                'type'          =>  'store',
                'store_all'     =>  true,
                'store_view'    =>  true,
				'width'     => '150px',
                'sortable'      =>  false,
				/* 'renderer' => 'Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Store', */
                'filter_condition_callback' =>  array($this, '_filterStoreCondition'),
            ));
       };
	  $this->addColumn(
            'actions',
            array(
                'header' => $this->__('Actions'),
                'width' => '150px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'url' => array('base' => '*/*/edit'),
                        'field' => 'id'
                    ),
                    array(
                        'caption' => $this->__('Duplicate'),
                        'url' => array('base' => '*/*/duplicate'),
                        'field' => 'id',
                        'confirm' => $this->__('Are you sure you want to do this?')
                    ),
                    array(
                        'caption' => $this->__('Delete'),
                        'url' => array('base' => '*/*/delete'),
                        'field' => 'id',
                        'confirm' => $this->__('Are you sure you want to do this?')
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
            )
        );
		$this->addExportType('*/*/exportCsv', Mage::helper('producttab')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('producttab')->__('XML'));
      return parent::_prepareColumns();
  }
	  protected function _prepareMassaction()
    {
        $this->setMassactionIdField('arw_tab_id');
        $this->getMassactionBlock()->setFormFieldName('producttab');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('producttab')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('producttab')->__('Are you sure?')
        ));
        return $this;
    }
  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }
	protected function _filterStoreCondition($collection, $column)
    {
        
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
    
        $this->getCollection()->addStoreFilter($value);
        
    }
		protected function _afterLoadCollection()
    {
        
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
        
    }
}