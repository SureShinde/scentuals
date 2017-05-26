<?php
class ARW_Brand_Block_Adminhtml_Brand_Renderer_Gridlogo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	
	 protected static $img = "";
	 protected static $html = "";
	 protected static $imgPath = "";
    public function render(Varien_Object $row)
    {
        return $this->_getValue($row);
    }
    
    protected function _getValue(Varien_Object $row)
    {
        $dored = false;
        if ($getter = $this->getColumn()->getGetter()) {
            $val = $row->$getter();
        }
		$brand_id  = $row->getData("brand_id");
		
		try {	
			$obj = $collection = Mage::getModel('brand/brand')->load($brand_id)->getData();
			$img = $obj["logo"];
			
			$x = 75;  
        	$y = 75; 
			$color = "255,255,255";
		
			if($img != "") {
				$imgPath = Mage::helper('brand')->getResizedUrl($img,$x,$y,$color);	
				
			} else {		
				$imageFile =  "arw/brand/files/b/r/brands.png";
				$imgPath = Mage::helper('brand')->getResizedUrl($imageFile,$x,$y,$color);
				
			}

			$html = "<img src='".$imgPath."' border=0 />";
			
		} catch(Exception $e){}
		
		return $html; 
	}


}
