<?php

class ARW_ProductTab_Model_Source_Categories extends Varien_Object
{
 
    const REPEATER = '_';
 
    const PREFIX_END = '';
 
    protected $_options = array();

    public function getOptionArray($parentId = 1, $recursionLevel)
    {
        $recursionLevel = (int)$recursionLevel;
        $parentId       = (int)$parentId;
 
        $category = Mage::getModel('catalog/category');
      
        $storeCategories = $category->getCategories($parentId, $recursionLevel, TRUE, FALSE, TRUE);
 
        foreach ($storeCategories as $node) {
   
 
            $this->_options[] = array(
 
                'label' => $node->getName().'-----'.$node->getEntityId(),
                'value' => $node->getEntityId()
            );
            if ($node->hasChildren()) {
                $this->_getChildOptions($node->getChildren());
            }
 
        }
 
        return $this->_options;
    }
    protected function _getChildOptions(Varien_Data_Tree_Node_Collection $nodeCollection)
    {
 
        foreach ($nodeCollection as $node) {
            $prefix = str_repeat(self::REPEATER, $node->getLevel() * 1) . self::PREFIX_END;
 
            $this->_options[] = array(
 
                'label' => $prefix . $node->getName().'------'.$node->getEntityId(),
                'value' => $node->getEntityId()
            );
 
            if ($node->hasChildren()) {
                $this->_getChildOptions($node->getChildren());
            }
        }
    }

 

}