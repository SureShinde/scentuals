<?php
class ARW_ProductTab_Block_Adminhtml_Producttab_Information_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
		$form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                   // 'action' => $this->getUrl('*/*/save', $params),
                    'method' => 'post',
                    'enctype' => 'multipart/form-data'
                ));
       $fieldsetStores = $form->addFieldset('store_section', array());
	   $fieldsetStores->addType('arw_store', 'ARW_ProductTab_Block_Adminhtml_Widget_Form_Element_Store');
	   $fieldsetStores->addField(
                'arw_store_id',
                'arw_store',
                array(
                    'label'    => $this->__('Store View'),
                    'name'     => 'store_id',
                //    'required' => true,
                )
            );
		$fieldsetCat = $form->addFieldset('cat_section', array());
		$fieldsetCat->addField('arw_product_categories', 'multiselect',
            array(
                'name'     => 'arw_product_categories[]',
                'label'    => 'Product Categories',
                'title'    => 'Product Categories',
                'required' => FALSE,
                'values'   => Mage::getModel('producttab/source_categories')->getOptionArray(),
				'disabled' => true,
				'readonly' => true,
            ));
		$productTypeNote="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::NONE."</span>---".ARW_ProductTab_Model_Product_Type::NONE_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::RANDOM."</span>---".ARW_ProductTab_Model_Product_Type::RANDOM_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::BESTSELL."</span>---".ARW_ProductTab_Model_Product_Type::BESTSELL_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::TOPRATED."</span>---".ARW_ProductTab_Model_Product_Type::TOPRATED_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::MOSTREVIEWED."</span>---".ARW_ProductTab_Model_Product_Type::MOSTREVIEWED_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::RECENTLYADDED."</span>---".ARW_ProductTab_Model_Product_Type::RECENTLYADDED_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::NEWADD."</span>---".ARW_ProductTab_Model_Product_Type::NEWADD_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::SPECIAL."</span>---".ARW_ProductTab_Model_Product_Type::SPECIAL_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::LASTORDERS."</span>---".ARW_ProductTab_Model_Product_Type::LASTORDERS_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::DISCOUNT."</span>---".ARW_ProductTab_Model_Product_Type::DISCOUNT_LABEL."</p>";
		$productTypeNote.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY."</span>---".ARW_ProductTab_Model_Product_Type::CURRENTCATEGORY_LABEL."</p>";
		$productTypeSortType="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Productsort::NEWFIRST."</span>---".ARW_ProductTab_Model_Product_Productsort::NEWFIRST_LABEL."</p>";
		$productTypeSortType.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Productsort::OLDFIRST."</span>---".ARW_ProductTab_Model_Product_Productsort::OLDFIRST_LABEL."</p>";
		$productTypeSortType.="<p><span style='color:red'>".ARW_ProductTab_Model_Product_Productsort::RANDOM."</span>---".ARW_ProductTab_Model_Product_Productsort::RANDOM_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::RANDOM."</span>---".ARW_ProductTab_Model_Category_Current_Type::RANDOM_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::BESTSELL."</span>---".ARW_ProductTab_Model_Category_Current_Type::BESTSELL_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::TOPRATED."</span>---".ARW_ProductTab_Model_Category_Current_Type::TOPRATED_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::MOSTREVIEWED."</span>---".ARW_ProductTab_Model_Category_Current_Type::MOSTREVIEWED_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::RECENTLYADDED."</span>---".ARW_ProductTab_Model_Category_Current_Type::RECENTLYADDED_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::NEWADD."</span>---".ARW_ProductTab_Model_Category_Current_Type::NEWADD_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::SPECIAL."</span>---".ARW_ProductTab_Model_Category_Current_Type::SPECIAL_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::LASTORDERS."</span>---".ARW_ProductTab_Model_Category_Current_Type::LASTORDERS_LABEL."</p>";
		$currentCatType.="<p><span style='color:red'>".ARW_ProductTab_Model_Category_Current_Type::DISCOUNT."</span>---".ARW_ProductTab_Model_Category_Current_Type::DISCOUNT_LABEL."</p>";
		$fieldsetPType=$form->addFieldset('produc_type_section', array());
		$fieldsetPType->addField('arw_produc_type', 'note', array(
          'text'     => Mage::helper('producttab')->__($productTypeNote),
		  'label'	=>"Product Type",
        ));
		$fieldsetPSortType=$form->addFieldset('produc_sort_type_section', array());
		$fieldsetPSortType->addField('arw_produc_sort_type', 'note', array(
          'text'     => Mage::helper('producttab')->__($productTypeSortType),
		  'label'	=>"Product Sort",
        ));		
		$fieldsetCurrentCatType=$form->addFieldset('current_cat_type_section', array());
		$fieldsetCurrentCatType->addField('cat_type_section', 'note', array(
          'text'     => Mage::helper('producttab')->__($currentCatType),
		  'label'	=>"Current Categories Type",
        ));	
		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
  }
}