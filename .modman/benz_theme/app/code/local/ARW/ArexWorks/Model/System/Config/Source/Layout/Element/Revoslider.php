<?php
class ARW_ArexWorks_Model_System_Config_Source_Layout_Element_Revoslider
{
	public function toOptionArray()
    {
		$modules = Mage::getConfig()->getNode('modules')->children();
		$modulesArray = (array)$modules;
		$enabled=Mage::getStoreConfig('advanced/modules_disable_output/AM_RevSlider')==0?true:false;
		$active=Mage::getConfig()->getModuleConfig('AM_RevSlider')->is('active', 'true');
		if($enabled&&$active) {
			$option=array();
			$sliders=Mage::getModel('revslider/slider')->getCollection();
            $option[]=array('value'=>'0','label'=>'None');
			foreach($sliders as $slider){
				$option[]=array('value'=>$slider->getId(),'label'=>$slider->getTitle());
				}
				return $option;
		}else {
			return;
		}
	}
}