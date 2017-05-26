<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Manifest_Shipment_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Initialization
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_shipment_grid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');        
    }
    
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        if($this->getFilterVisibility() && Mage::helper('chcanpost2module/rest_request')->isContract()){
            $manifest_id = $this->getRequest()->getParam('manifest_id');
            if (!empty($manifest_id)) {
                $manifest = Mage::getModel('chcanpost2module/manifest')->load($manifest_id);
                $manifest_url = $manifest->getUrl();
                if (!empty($manifest_url)) {
                    $html.= $this->getLayout()->createBlock('adminhtml/widget_button')
                                ->setData(array(
                                    'label'     => Mage::helper('adminhtml')->__('Print CP Manifest'),
                                    'onclick'   => 'setLocation(\''.$this->getPrintManifest($manifest_id).'\')',
                                    'class'   => 'task'
                                ))->toHtml();
                }
            }
        }
        return $html;
    }
    
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_shipment_grid_collection';
    }

    /**
     * Prepare and set collection of grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {

        $manifest_id = $this->getRequest()->getParam('manifest_id');

        $collection = Mage::getModel('chcanpost2module/order_shipment')->prepareGridCollection($manifest_id);

	$collection->getSelect()->where('o.shipping_method != \'chcanpost2module_failure\'');

	$collection->getSelect()->joinLeft(array('store' => Mage::getSingleton('core/resource')->getTableName('core/store')), 'main_table.store_id=store.store_id', array('store_name' => 'name'));

        $this->setCollection($collection);
        
        return parent::_prepareCollection();

    }

    /**
     * Prepare and add columns to grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        
        $this->addColumn('shipment_increment_id', array(
            'header'    => Mage::helper('sales')->__('Shipment #'),
            'index'     => 'shipment_increment_id',
            'filter_index'     => 'main_table.increment_id',
            'type'      => 'text',
        ));

	$allStores = Mage::app()->getStores();

	$storeFiler = array();

	foreach ($allStores as $storeId=>$val) {

		$storeFiler[$storeId] = Mage::app()->getStore($storeId)->getName();

	}

        $this->addColumn('store_name', array(
            'header'    => Mage::helper('sales')->__('Store'),
            'index'     => 'store_name',
            'filter_index' => 'main_table.store_id',
            'type'      => 'options',
	    'options' => $storeFiler
        ));
        
        $this->addColumn('manifest_id', array(
            'header'    => Mage::helper('sales')->__('Is in Manifest'),
            'index'     => 'manifest_id',
            'type'      => 'text',
            'renderer'  => 'Collinsharper_Canpost_Block_Renderer_Manifest_Shipment_Status',
            'filter'    => false
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('sales')->__('Date Shipped'),
            'index'     => 'created_at',
            'filter_index'     => 'main_table.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('sales')->__('Order #'),
            'index'     => 'order_increment_id',
            'filter_index'     => 'o.increment_id',
            'type'      => 'text',
        ));

        $this->addColumn('order_created_at', array(
            'header'    => Mage::helper('sales')->__('Order Date'),
            'index'     => 'order_created_at',
            'filter_index'     => 'o.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('ordered_by', array(
            'header' => Mage::helper('sales')->__('Ordered By'),
            'index' => 'ordered_by',
            'filter_index' => 'CONCAT(o.customer_firstname, \' \',o.customer_lastname)',
        ));

        $this->addColumn('total_qty', array(
            'header' => Mage::helper('sales')->__('Total Qty'),
            'index' => 'total_qty',
            'filter_index' => 'main_table.total_qty',
            'type'  => 'number',
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('sales')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('sales')->__('View'),
                        'url'     => array('base'=>'*/sales_shipment/view'),
                        'field'   => 'shipment_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'is_system' => true
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Get url for row
     *
     * @param string $row
     * @return string
     */
    public function getRowUrl($row)
    {
        if (!Mage::getSingleton('admin/session')->isAllowed('sales/order/shipment')) {
            return false;
        }

        return $this->getUrl('*/sales_shipment/view',
            array(
                'shipment_id'=> $row->getId(),
            )
        );
    }

    /**
     * Prepare and set options for massaction
     *
     * @return Mage_Adminhtml_Block_Sales_Shipment_Grid
     */
    protected function _prepareMassaction()
    {
        
        $manifest_id = $this->getRequest()->getParam('manifest_id');

        $this->setMassactionIdField('main_table.entity_id');
        
        $this->getMassactionBlock()->setFormFieldName('shipment_ids');
        
        if (empty($manifest_id)) {
            $this->getMassactionBlock()->addItem('create_shipments', array(
                 'label'=> Mage::helper('sales')->__('Create Canada Post shipments'),
                 'url'  => $this->getUrl('*/*/massCreate'),
            ));
        } else {
            $manifest = Mage::getModel('chcanpost2module/manifest')->load($manifest_id);
            if ($manifest->getStatus() == 'pending') {
                $this->getMassactionBlock()->addItem('add_shipments', array(
                     'label'=> Mage::helper('sales')->__('Add shipments to manifest'),
                     'url'  => $this->getUrl('*/*/massAdd').'manifest_id/'.$manifest_id
                ));
                if (Mage::helper('chcanpost2module/rest_shipment')->isContract()) {
                    $this->getMassactionBlock()->addItem('remove_shipments', array(
                         'label'=> Mage::helper('sales')->__('Remove shipments from manifest'),
                         'url'  => $this->getUrl('*/*/massRemove').'manifest_id/'.$manifest_id,
                    ));
                }
            }
            $this->getMassactionBlock()->addItem('create_shipments', array(
                 'label'=> Mage::helper('sales')->__('Print Canada Post labels'),
                 'url'  => $this->getUrl('*/shipping/massLabel'),
            ));
        }
        
        return $this;
        
    }

    /**
     * Get url of grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/*', array('_current' => true));
    }
    
    public function getPrintManifest($manifest_id)
    {
        return $this->getUrl('*/manifest/print', array(
            'manifest_id' => $manifest_id
        ));
    }
    

}
