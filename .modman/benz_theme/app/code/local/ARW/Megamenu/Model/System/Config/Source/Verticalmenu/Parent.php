<?php

class ARW_Megamenu_Model_System_Config_Source_Verticalmenu_Parent
{
    public function toOptionArray()
    {
        return array(
         array('value' => 'root','label' => Mage::helper('megamenu')->__('Root - show top-level categories')),
         array('value' => 'parent','label' => Mage::helper('megamenu')->__('Parent of current category - show current category and its siblings')),
         array('value' => 'parent_no_siblings','label' => Mage::helper('megamenu')->__('Parent of current category (no siblings) - show current category')),
          array('value' => 'current','label' => Mage::helper('megamenu')->__('Current category - show subcategories of current category')),
        );
    }
}
