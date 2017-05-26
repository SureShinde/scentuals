<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Block_Renderer_Manifest_Shipment_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        
        $value = $row->getData($this->getColumn()->getIndex());

        return !empty($value) ? '<span style="color:green;">'.Mage::helper('chcanpost2module')->__('In Manifest').'</span>' : '-';
        
    }

}
