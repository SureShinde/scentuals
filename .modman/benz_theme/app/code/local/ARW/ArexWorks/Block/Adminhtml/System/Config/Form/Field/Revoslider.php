<?php
class ARW_ArexWorks_Block_Adminhtml_System_Config_Form_Field_Revoslider extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){ 
       	$html = parent::_getElementHtml($element);  
		$active=Mage::getConfig()->getModuleConfig('AM_RevSlider')->is('active', 'true');
		$enabled=Mage::getStoreConfig('advanced/modules_disable_output/AM_RevSlider')==0?true:false;
		
		if(!$active||!$enabled){
			$html .= ' 
				<script type="text/javascript">
						$("row_'. $element->getHtmlId() .'").setStyle({display: "none"});
				</script>
			';
		}else{
			$sliders=Mage::getModel('revslider/slider')->getCollection();
			if($sliders->count()<=0){
				$html .= ' 
				<script type="text/javascript">
						$("row_'. $element->getHtmlId() .'").setStyle({display: "none"});
				</script>
			';
			}else{
				return $html;
			}
		}
        return $html;
    }
}
?>