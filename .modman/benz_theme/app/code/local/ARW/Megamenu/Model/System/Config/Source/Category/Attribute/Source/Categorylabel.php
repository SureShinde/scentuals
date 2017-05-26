<?php
?>
<?php

class ARW_Megamenu_Model_System_Config_Source_Category_Attribute_Source_CategoryLabel extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	protected $_options;
	
	/**
     * Get list of existing category labels
     */
	public function getAllOptions()
	{
		$cfg = Mage::helper('megamenu');
		
		if (!$this->_options)
		{	
			$tmp=$cfg->getCfg('category_label/label');
			$arrayLabel=explode(',', $tmp);
			$this->_options[]=array('value'=>'','label'=>'');
			foreach ($arrayLabel as $key=>$value)
			{				
				$this->_options[]=array('value' => 'label'.$key, 'label' =>$value);
			}
        return $this->_options;
    	}
	}
}
