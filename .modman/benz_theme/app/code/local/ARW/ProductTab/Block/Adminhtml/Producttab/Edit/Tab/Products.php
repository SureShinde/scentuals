<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Edit_Tab_Products extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm(){
	
		$form = new Varien_Data_Form();
		$tabtModel = Mage::registry('tab_data');
		
        $this->setForm($form);
		$arw_tab_id=$tabtModel->getData('arw_tab_id');
		$productRow=Mage::getModel("producttab/tab")->getArwProductData($arw_tab_id);
		$product_data=array('product_type'=>$productRow['product_type'],
							'product_sort_type'=>$productRow['product_sort_type'],
							'product_data'=>$productRow['product_data'],
							'current_category_type'=>$productRow['current_category_type']
		);
		$data=array();
		$data=array_merge($tabtModel->getData(),$product_data);
		
        $fieldset = $form->addFieldset('product_settings', array('legend' => $this->__('Filter Products')));
        $fieldset->addField(
            'product_type', 'select',
            array(
                'name' => 'product_type',
                'label' => $this->__('Product Type'),
                'values' => Mage::getModel('producttab/product_type')->toOptionArray()
            )
        );

        $fieldset->addField(
            'product_sort_type', 'select',
            array(
                'name' => 'product_sort_type',
                'label' => $this->__('Product Sorting Type'),
                'values' => Mage::getModel('producttab/product_productsort')->toOptionArray()
            )
        );

         $dataProduct = array();
        $productGridHtml = Mage::getSingleton('core/layout')
            ->createBlock(
                'producttab/adminhtml_producttab_edit_tab_product_grid_grid', null,
                 array('section_data_product' => $dataProduct) 
            )
            ->toHtml()
        ;
        $fieldset->addField(
            'grid_product', 'note',
            array(
				'name'	=>'grid_product',
                'label' => $this->__('Select Products'),
                'text' => $productGridHtml
            )
        );
         $category_grid = Mage::getSingleton('core/layout')
            ->createBlock('producttab/adminhtml_producttab_edit_tab_category_categoriestree')
            ->toHtml()
        ;
        $fieldset->addField(
            'tree_categories', 'note',
            array(
				'name'=>'tree_categories',
                'label' => $this->__('Select Categories'),
                'text' => $category_grid
            )
        );
        $fieldset->addField(
            'current_category_type', 'select',
            array(
                'name' => 'current_category_type',
                'label' => $this->__('Type of product in current category'),
                'values' => Mage::getModel('producttab/category_current_type')->toOptionArray()
            )
        ); 
	    if (version_compare(Mage::getVersion(), '1.7.0.0') < 0){
        //    $dependenceElement = $this->getLayout()->createBlock('arexmage/adminhtml_widget_form_element_dependence');
        }else{
            $dependenceElement = $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence');
        }
	$arrDependCat=array(
					(string)ARW_ProductTab_Model_Product_Type::RANDOM,
					(string)ARW_ProductTab_Model_Product_Type::BESTSELL,
					(string)ARW_ProductTab_Model_Product_Type::TOPRATED,
					(string)ARW_ProductTab_Model_Product_Type::MOSTREVIEWED,
					(string)ARW_ProductTab_Model_Product_Type::RECENTLYADDED,
					(string)ARW_ProductTab_Model_Product_Type::NEWADD,
					(string)ARW_ProductTab_Model_Product_Type::LASTORDERS,
					(string)ARW_ProductTab_Model_Product_Type::DISCOUNT,
	);
	$this->setChild('form_after', $dependenceElement
					->addFieldMap($form->getHtmlIdPrefix().'product_type','product_type')
					->addFieldMap($form->getHtmlIdPrefix().'product_sort_type','product_sort_type')
					->addFieldMap($form->getHtmlIdPrefix().'grid_product','grid_product')
					->addFieldMap($form->getHtmlIdPrefix().'current_category_type','current_category_type')
					->addFieldMap($form->getHtmlIdPrefix().'tree_categories','tree_categories')
					->addFieldDependence(
						'grid_product','product_type',ARW_ProductTab_Model_Product_Type::NONE
					)
					->addFieldDependence(
						'product_sort_type','product_type',ARW_ProductTab_Model_Product_Type::NONE
					)
					->addFieldDependence(
						'current_category_type','product_type',ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY
					)
					->addFieldDependence(
						'tree_categories','product_type',$arrDependCat
					)
				);
		$form->setValues($data);
	  return parent::_prepareForm();
    }
	
}