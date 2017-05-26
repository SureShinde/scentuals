<?php 
class ARW_ProductTab_Block_Adminhtml_Producttab_Renderer_Store extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        $storeIdsArr = $row->getStoreId();
        $storeIdsStr = implode(",", $storeIdsArr);
        return $storeIdsStr;        
    }
}