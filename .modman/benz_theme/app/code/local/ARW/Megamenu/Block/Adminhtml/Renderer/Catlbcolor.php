<?php
class ARW_Megamenu_Block_Adminhtml_Renderer_Catlbcolor extends Varien_Data_Form_Element_Text
{
	 public function getAfterElementHtml()
		{
			$html = parent::getAfterElementHtml();
			$jsPath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'arw/arexworks/jquery/jquery-1.8.2.min.js';
			$mcPath = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_JS).'arw/arexworks/jquery/mcolorpicker/';
		
		if (Mage::registry('jqueryLoaded') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $jsPath .'"></script>
			<script type="text/javascript">jQuery.noConflict();</script>
			';
			Mage::register('jqueryLoaded', 1);
        }
		if (Mage::registry('colorPickerLoaded') == false)
		{
			$html .= '
			<script type="text/javascript" src="'. $mcPath .'mcolorpicker.js"></script>
			<script type="text/javascript">
				jQuery.fn.mColorPicker.init.replace = false;
				jQuery.fn.mColorPicker.defaults.imageFolder = "'. $mcPath .'images/";
				jQuery.fn.mColorPicker.init.allowTransparency = true;
				jQuery.fn.mColorPicker.init.showLogo = false;
			</script>
            ';
			Mage::register('colorPickerLoaded', 1);
        }
		
			$html .= '
				<style> .fieldset-wide .form-list td.value input.mColorPicker{width:40% !important} </style>
				<script type="text/javascript">
					jQuery(function($){
						$("#'. $this->getHtmlId() .'").attr("data-hex", true).width("250px").mColorPicker();
					});
				</script>
			';
			return $html;
		} 	
}