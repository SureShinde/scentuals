<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Mysql4_Manifest_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    
    
    protected function _construct()
    {
        
        $this->_init('chcanpost2module/manifest');
        
    } 
    
    
}
