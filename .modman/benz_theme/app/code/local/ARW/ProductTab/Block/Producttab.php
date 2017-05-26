<?php
class ARW_ProductTab_Block_Producttab extends Mage_Catalog_Block_Product_Abstract implements Mage_Widget_Block_Interface 
{
	public function getConfig($name)
	{
		switch($name){
			case "tab_identifier" :
				$tab_identifiers = explode(',',$this->getData('tab_identifier'));
				return $tab_identifiers;
				break;
			case "identifier" :
				return $this->getData('identifier');
				break;
			default:
                return $this->getData($name);
		}
	}
	public function getIdByIdentifier()
	{
		$tab_id=array();
		$array_identifiers=$this->getConfig('tab_identifier');
		foreach($array_identifiers as  $identifier)
        {
			$collection =   Mage::getModel('producttab/tab')->getCollection()
                            ->addFieldToFilter('arw_identifier',$identifier)
                            ->getFirstItem()
                            ->getData();
            $tab_id[]= isset($collection['arw_tab_id']) ? $collection['arw_tab_id'] : array();
		}
        return $tab_id;
	}
	public function getFilterStore()
	{
		$collection =   Mage::getModel('producttab/tab')->getCollection()
                        ->addStoreFilter(Mage::app()->getStore(true)->getId());
		return $collection;
    }
	public function getArrayTabsFilterStore()
	{
		$tabsIdArray=array();
		$collection=$this->getFilterStore()->getData();
		foreach($collection as $con)
		{
			$tabsIdArray[]=$con['arw_tab_id'];
        }
		if($tabs=$this->getIdByIdentifier())
		{
			$tabsFilterStore = array_intersect($tabs,$tabsIdArray);
		}
        else
		{
			$tabsFilterStore=$tabsIdArray;
		}

		return $tabsFilterStore;
	}
	public function getTabs()
    {
        $return = array();
        $array_tmp = array();
        $collection=Mage::getModel('producttab/tab')->getCollection();
        $tabsFilterStore = $this->getArrayTabsFilterStore();
        $collection->addFieldToFilter('arw_tab_id',array('in'=>$tabsFilterStore));
        $collection->getData();
        // Prepare order
        if($collection->count())
        {
            $counter = 0;
            foreach($collection as $value)
            {
                $array_tmp[$counter] = array(
                    'arw_tab_id' => $value['arw_tab_id'],
                    'arw_name' => $value['arw_name']
                );
                $counter++;
            }
            if(count($tabsFilterStore))
            {
                $counter = 0;
                foreach($tabsFilterStore as $tab_id)
                {
                    foreach($array_tmp as $value)
                    {
                        if(isset($value['arw_tab_id']) && $value['arw_tab_id'] == $tab_id)
                        {
                            $return[$counter] = $value;
                            $counter++;
                            break;
                        }
                    }
                }
            }
        }
        return $return;
    }

    public function getTabFirstHtml()
    {
        if (!$this->_pager)
        {
            $this->_pager   =   $this->getLayout()
                                ->createBlock('producttab/producttab', 'producttab.first')
                                ->setTemplate('arw/producttab/productfirst.phtml');
            $this->_pager->setEnableCountDown($this->getConfig('countdown'));
            $this->_pager->setCollection($this->_getProductFirstCollection());
            $this->_pager->setIdentifier($this->getData('identifier'));
            $this->_pager->setFirst($this->getTabFirst());
        }
        if ($this->_pager instanceof Mage_Core_Block_Abstract)
        {
            return $this->_pager->toHtml();
        }
        return '';
    }

	public function getTabFirst()
	{
		$collection             =   Mage::getModel('producttab/tab');
		$tabsFilterStore        =   $this->getArrayTabsFilterStore();
		$tabsFilterStorefirst   =   $tabsFilterStore[0];
		$collection->load($tabsFilterStorefirst); 
		return $collection->getData();
	}
	public function getTabslider()
	{
        if($id = $this->getRequest()->getParam('ajax_tab_id')){
			$collection=Mage::getModel('producttab/tab')->load($id)->getData();
			return $collection;
		}
	}
	public function getLoadedProductCollection($data)
    {
        if (!$this->_productCollection)
        {
            $product_type   =   $data['product_type'];
			$tab_id	        =   $data['arw_tab_id'];
            $collection     =   null;
			$array_category =   $this->getArWCatIds($data);
            switch ($product_type)
            {
                case ARW_ProductTab_Model_Product_Type::NONE:
                    $collection = $this->getNoneCollection($array_category,$tab_id,$data['product_sort_type']);
                    break;
                case ARW_ProductTab_Model_Product_Type::RANDOM:
                    $collection = $this->getRandomCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Product_Type::BESTSELL:
                    $collection = $this->getBestSellerCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Product_Type::TOPRATED:
                    $collection = $this->getTopRateCollection($array_category,$tab_id);
                    break;
				case ARW_ProductTab_Model_Product_Type::MOSTREVIEWED:
                    $collection = $this->getMostViewedCollection($array_category,$tab_id);
                    break;
				case ARW_ProductTab_Model_Product_Type::RECENTLYADDED:
                    $collection = $this->getRecentlyAddedCollection($array_category,$tab_id);
                    break;
				
				case ARW_ProductTab_Model_Product_Type::NEWADD:
                    $collection = $this->getNewCollection($array_category,$tab_id);
                    break; 	
				case ARW_ProductTab_Model_Product_Type::LASTORDERS:
                    $collection = $this->getLastOrderedCollection($array_category,$tab_id);
                    break;
				case ARW_ProductTab_Model_Product_Type::DISCOUNT:
                    $collection = $this->getDiscountCollection($array_category,$tab_id);
					break;
				case ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY:
					if($array_category)
                    {
						$collection=$this->getLoadCurrentCatCollection($array_category,$data['current_category_type'],$tab_id);
					}
                    else
                    {
						$collection = $this->getNoneCollection($array_category,$tab_id);
					}
					break;
            }
            Mage::dispatchEvent('catalog_block_product_list_collection', array(
                'collection' => $collection
            ));
            $this->_productCollection = $collection;
        }
        return $this->_productCollection;
    }
	protected function getLoadCurrentCatCollection($array_category,$current_type,$tab_id)
    {
        if(!$this->_productsCollection)
        {
            $collection = null;
            switch ($current_type)
            {
                case ARW_ProductTab_Model_Category_Current_Type::RANDOM:
                    $collection = $this->getRandomCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::BESTSELL:
                    $collection = $this->getBestSellerCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::TOPRATED:
                    $collection = $this->getTopRateCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::MOSTREVIEWED:
                    $collection = $this->getMostViewedCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::RECENTLYADDED:
                    $collection = $this->getRecentlyAddedCollection($array_category,$tab_id);
                    break;

                case ARW_ProductTab_Model_Category_Current_Type::NEWADD:
                    $collection = $this->getNewCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::LASTORDERS:
                    $collection = $this->getLastOrderedCollection($array_category,$tab_id);
                    break;
                case ARW_ProductTab_Model_Category_Current_Type::DISCOUNT:
                    $collection = $this->getDiscountCollection($array_category,$tab_id);
                    break;
            }
            $this->_productsCollection = $collection;
        }
        return $this->_productsCollection;
	}
	public function _getProductCollection()
    {	
	    if($this->getRequest()->getParam('ajax_tab_id'))
        {
            if($this->getRequest()->getParam('countdown'))
            {
                $this->setEnableCountDown(true);
            }
            else
            {
                $this->setEnableCountDown(false);
            }
			$tab_id     =   $this->getRequest()->getParam('ajax_tab_id');
			$data       =   $this->loadTableDataById($tab_id);
			$collection =   $this->getLoadedProductCollection($data);
			return $collection;
		}
	}
	public function _getProductFirstCollection()
    {	
        $tab        =   $this->getTabFirst();
        $tab_id     =   $tab['arw_tab_id'];

        $data       =   $this->loadTableDataById($tab_id);
        $collection =   $this->getLoadedProductCollection($data);
        return $collection;
	}
	public function loadTableDataById($tab_id)
	{
		$collection =   Mage::getModel('producttab/product')->getCollection()
                        ->addFieldToFilter('arw_tab_id',$tab_id)
                        ->getFirstItem();
		return $collection->getData();
	}
	public function getLimitProduct($tab_id)
    {	
		$collection =   Mage::getModel('producttab/tab')->load($tab_id)->getData();
        $limit  =   $collection['arw_limit'];
		if( $collection['arw_use_default'] && $collection['arw_use_default'] ==1 )
        {
			$limit  =   Mage::helper('producttab')->getCf('limit');
		}
		return $limit ;
	}
	protected function getArWCatIds($data)
    {
        $array_category_id = array();
		if($data['product_type']==ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY)
        {
            if(Mage::registry('current_category') && Mage::registry('current_category')->getId())
            {
                $array_category_id[] = Mage::registry('current_category')->getId();
            }
        }
        else
        {
            $array_category_id = explode(',',$data['product_data']);
	    }
	    return $array_category_id;
    }
	public function getNoneCollection($array_product_id = array(),$tab_id,$sort = null)
    {	
        if (is_null($this->_productCollection))
        {
            $array_product_id = array_unique($array_product_id);
			$product_collection = Mage::getResourceModel('catalog/product_collection');
            $product_attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
            $product_collection->addAttributeToSelect($product_attributes)
                ->addMinimalPrice()
                ->addFinalPrice() 
                ->addTaxPercents()
                ->addStoreFilter()
                ->addIdFilter($array_product_id);
            switch($sort)
            {
				case ARW_ProductTab_Model_Product_Productsort::NEWFIRST:
                    $product_collection->getSelect()->order('entity_id desc');
					break;
				case ARW_ProductTab_Model_Product_Productsort::OLDFIRST:
                    $product_collection->getSelect()->order('entity_id asc');
					break;
				case ARW_ProductTab_Model_Product_Productsort::RANDOM:
                    $product_collection->getSelect()->order('RAND()');
					break;
			}
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($product_collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($product_collection);
            $product_collection->setPage(1, $this->getLimitProduct($tab_id));
            $product_collection->load();
            $this->_productCollection = $product_collection;
        }
        return $this->_productCollection;
    }
    protected function getRandomCollection($array_category,$tab_id)
    {
        $array_product_id = $this->getProductIdsByCategories($array_category);
        $product_collection = Mage::getResourceModel('catalog/product_collection');
        Mage::getModel('catalog/layer')->prepareProductCollection($product_collection);
        $product_collection
            ->getSelect()
            ->order('RAND()');
        $product_collection->addStoreFilter();
        if (count($array_product_id))
        {
            $product_collection->addIdFilter($array_product_id);
        }
        $product_collection->setPage(1,$this->getLimitProduct($tab_id));
        return $product_collection;
    }
    protected function getMostViewedCollection($array_category = null,$tab_id)
    {
        $_ids = Mage::getResourceModel('reports/product_collection')->addViewsCount()->load()->getLoadedIds();
        $_id_filter = $_ids;
        if(!empty($array_category))
        {
            $_id_filter = array_intersect($_ids, $this->getProductIdsByCategories($array_category));
        }
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addIdFilter($_id_filter);
        $product_collection->getSelect()->order(sprintf('FIELD(e.entity_id, %s)', implode(',', $_id_filter)));
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
    }
    protected function getBestSellerCollection($array_category,$tab_id)
    {
        $product_collection = Mage::getModel('catalog/product')->getCollection();
        $product_collection->addAttributeToSelect('*')->addStoreFilter();
        if(!empty($array_category))
        {
            $_category_id_filter = array();
            foreach ($array_category as $_category_id)
            {
                $_category_id_filter[]['finset'] = $_category_id;
            }
            $product_collection
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                ->addAttributeToFilter('category_id', array($_category_id_filter));
        }

        $_order_item = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
        $_order =  Mage::getSingleton('core/resource')->getTableName('sales/order');
        $product_collection
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->getSelect()
            ->join(array('items' => $_order_item), "items.product_id = e.entity_id", array('count' => 'SUM(items.qty_ordered)'))
            ->join(array('trus' => $_order), "items.order_id = trus.entity_id", array())
            ->where('trus.status = ?', 'complete')
            ->group('e.entity_id')
            ->order('count DESC');
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
    }
	protected function getTopRateCollection($array_category,$tab_id)
    {
        $product_collection = Mage::getModel('catalog/product')->getCollection();
        $product_collection->addAttributeToSelect('*')->addStoreFilter();
        if(!empty($array_category))
        {
            $_category_id_filter = array();
            foreach ($array_category as $_category_id)
            {
                $_category_id_filter[]['finset'] = $_category_id;
            }
            $product_collection
                ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                 ->addAttributeToFilter('category_id', array($_category_id_filter));
        }
        $_review_aggregate = Mage::getSingleton('core/resource')->getTableName('review/review_aggregate');
        $product_collection
			->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
			->getSelect()
            ->join(array('rat' => $_review_aggregate), "rat.entity_pk_value = e.entity_id", array())
         // ->where('rat.status = ?', 'complete')
            ->group('e.entity_id')
            ->order('rating_summary DESC');
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
	}
    protected function getRecentlyAddedCollection($array_category,$tab_id)
    {
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        if(!empty($array_category))
        {
            $_array_product_id = $this->getProductIdsByCategories($array_category);
            $product_collection->addIdFilter($_array_product_id);
        }
        $product_collection->addAttributeToSort('created_at', 'desc');
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
    }
    protected function getNewCollection($array_category,$tab_id)
    {
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter();
        $_to_day = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

        if(!empty($array_category))
        {
            $array_product_id = $this->getProductIdsByCategories($array_category);
            $product_collection->addIdFilter($array_product_id);
        }

        $product_collection->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $_to_day))
            ->addAttributeToFilter(array(
                array('attribute' => 'news_to_date', 'date' => true, 'from' => $_to_day),
                array('attribute' => 'news_to_date', 'is' => new Zend_Db_Expr('null'))),
                '',
                'left')
            ->addAttributeToSort('news_from_date', 'desc');
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
    }
	protected function getLastOrderedCollection($array_category,$tab_id)
	{
        $store_id = Mage::app()->getStore()->getId();
        $order_item_collection = Mage::getResourceModel('sales/order_item_collection')
            ->join('order', 'order_id=entity_id')
            ->addFieldToFilter('main_table.store_id', array('eq'=>$store_id))
            ->setOrder('main_table.created_at','desc')
            ->getSelect()
            ->group(`main_table`.'product_id');

        $array_product_id = array();
        if(sizeof($order_item_collection)>0)
        {
            foreach ($order_item_collection as $_product)
            {
                $array_product_id[] =$_product->getProductId();
            }
        }

        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes()) ;
        if(!empty($array_category))
        {
            $array_product_id = array_intersect($array_product_id, $this->getProductIdsByCategories($array_category));
            $product_collection->addIdFilter($array_product_id);
        }
        $product_collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        if(!empty($array_product_id))
        {
            $product_collection
                ->getSelect()
                ->order(sprintf('FIELD(e.entity_id, %s)', implode(',', $array_product_id)));
        }
        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
	}
    protected function getDiscountCollection($array_category,$tab_id)
    {
        /*
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->getSelect()
            ->where('price_index.final_price < price_index.price');
        */
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('special_price', array('neq'=>''))
            ->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('special_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addMinimalPrice()
            ->addUrlRewrite()
            ->addTaxPercents()
            ->addStoreFilter()
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addAttributeToSort('special_from_date', 'desc');
        /*
        $resource = Mage::getSingleton('core/resource');
        $connection = $resource->getConnection('core_read');
        $tableName = $resource->getTableName('catalogrule/rule_product');
        $tableAlias = 'catalogrule_product_idx';
        $subSelect  = $connection->select()->from($tableName, array('product_id', 'customer_group_id'));

        $product_collection->getSelect()->join(
            array($tableAlias => $subSelect),
            join(' AND ', array(
                "{$tableAlias}.product_id = e.entity_id",
                $connection->quoteInto("{$tableAlias}.customer_group_id = ?", $this->_customerGroupId)
            )),
            array()
        );
        */

        if (!empty($array_category))
        {
            $array_product_id = $this->getProductIdsByCategories($array_category);
            if(count($array_product_id) > 0)
            {
                $product_collection->addIdFilter($array_product_id);
            }
        }

        $product_collection->setPage(1, $this->getLimitProduct($tab_id));
        $product_collection->load();
        return $product_collection;
    }
    public function getProductsByCategory($category)
    {
        $product_collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
            ->addFieldToFilter('visibility', Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->addFieldToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            ->addStoreFilter();
        if($category)
        {
            $_category = Mage::getModel('catalog/category')->load($category);
            if($_category->getId())
            {
                $product_collection->addCategoryFilter($_category);
            }
        }
        return $product_collection;
    }
    public function getProductIdsByCategories($array_category)
    {
        $array_product_id = array();
        if(is_array($array_category) && count($array_category) > 0)
        {
            foreach($array_category as $_category_id)
            {
                if (is_numeric($_category_id))
                {
                    $_array_product = $this->getProductsByCategory($_category_id);
                    if(count($_array_product))
                    {
                        foreach($_array_product as $_product)
                        {
                            $array_product_id[] = $_product->getId();
                        }
                    }
                }
            }
        }
        $array_product_id = array_unique($array_product_id);
        return $array_product_id;
    }
	public function getSendUrl()
    {
        $url = $this->getUrl('producttab/producttab/product');
        if (isset($_SERVER['HTTPS']) && 'off' != $_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "")
        {
            $url = str_replace('http:', 'https:', $url);
        }
        return $url;
    }
}