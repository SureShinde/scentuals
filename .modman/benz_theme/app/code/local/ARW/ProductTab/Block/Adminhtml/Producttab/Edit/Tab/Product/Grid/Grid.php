<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit_Tab_Product_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	 protected $_collection = null;
    public function __construct() {
        parent::__construct();
        $this->setTemplate('arw/producttab/catalog/product/widget-grid.phtml');
        $this->setId('producttabProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);

        if ($this->getGroupId() && $this->_getSelectedProducts()) {
            $this->setDefaultFilter(array('massaction' => 1));
        }
    }

    public function getGroupId() {
        return (int) Mage::app()->getFrontController()->getRequest()->getParam('group_id');
    }

    protected function _getHelper() {
        return Mage::helper('producttab');
    }

    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
	public function getCollection()
    {
        return $this->_collection;
    }
	 public function setCollection($collection)
    {
        $this->_collection = $collection;
    }

    protected function _prepareCollection() {
        $this->setDefaultFilter(array('massaction' => 1));
        
		$store = $this->_getStore();
        
                
        $collection = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('type_id')
                ->addAttributeToFilter('visibility', array('nin' => array(1,3)))
				->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');


        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $collection->addStoreFilter($store);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', 1, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');
        }
        /*  $_visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );
        $collection->addAttributeToFilter('visibility', $_visibility); */
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
        $this->setCollection($collection);        
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    private function _getProductType() {
        $res = array();
        $type = Mage::getSingleton('catalog/product_type')->getOptionArray();
        if ($type) {
            foreach ($type as $key => $value) {
               /*  if ($key != Mage_Catalog_Model_Product_Type::TYPE_GROUPED
                        && $key != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) { */
                    $res[$key] = $value;
               /*  } */
            }
        }
        return $res;
    }
	 protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection() && $column->getId() == 'websites') {
            $this->getCollection()->joinField(
                'websites',
                'catalog/product_website',
                'website_id',
                'product_id=entity_id',
                null,
                'left'
            );
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareColumns() {
        $helper = $this->_getHelper();
		$this->addColumn(
            'entity_id',
            array(
                'header' => Mage::helper('producttab')->__('ID'),
                'sortable' => true,
                'width' => '60',
                'index' => 'entity_id'
            )
        );

        $this->addColumn('name', array(
            'header' => $helper->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('type', array(
            'header' => $helper->__('Type'),
            'width' => 100,
            'index' => 'type_id',
            'type' => 'options',
            'options' => $this->_getProductType(),
        ));
         if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                array(
                    'header' => Mage::helper('producttab')->__('Websites'),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                )
            );
        }
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('set_name', array(
            'header' => $helper->__('Attrib. Set Name'),
            'width' => 100,
            'index' => 'attribute_set_id',
            'type' => 'options',
            'options' => $sets,
        ));

        $this->addColumn('sku', array(
            'header' => $helper->__('SKU'),
            'width' => 100,
            'index' => 'sku',
        ));

        $this->addColumn('price', array(
            'header' => $helper->__('Price'),
            'index' => 'price',
            'type' => 'currency',
            'currency_code'
            => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
        ));

        $this->addColumn('qty', array(
            'header' => $helper->__('Qty'),
            'width' => 100,
            'index' => 'qty',
            'type' => 'number',
            'validate_class'
            => 'validate-number',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('entity_id');
        $massBlock = $this->getMassactionBlock();
        $massBlock->setTemplate('arw/producttab/catalog/product/widget-grid-massaction.phtml');
        $massBlock->addItem(null, array());

        $productIds = $this->_getSelectedProducts();
        if ($productIds) {
            $massBlock->getRequest()->setParam($massBlock->getFormFieldNameInternal(), $productIds);
        }

        return $this;
    }

    private function _getSelectedProducts() {
        $productIds = '';
        $session = Mage::getSingleton('adminhtml/session');
        if ($data = $session->getData('tab_data')) {
            if (isset($data['in_products'])) {
                $productIds = $data['in_products'];
            }
            $session->setData('tab_data', null);
        } elseif (Mage::registry('tab_data')) {
            $productIds = Mage::registry('tab_data')->getData('in_products');
        }
		/* var_dump($productIds);die(); */
        return $productIds;
    }
    
    public function category_filter($collection, $column) {
        $cond = $column->getFilter()->getCondition();
        if (empty($cond['eq'])) {
            return true;
        }
        $where = 'category.category_id = ' . $cond['eq'];
        $collection->getSelect()->where($where);
    }
    
    
    public function getGridUrl() {
        $url = $this->getUrl('*/*/productGrid', array('_current' => true));
        if (strpos($url, 'internal_massaction') !== false) {
            $url = substr($url, 0, strpos($url, 'internal_massaction'));
        }
        return $url;
    }
}
