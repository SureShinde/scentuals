<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Mysql4_Office extends Mage_Core_Model_Mysql4_Abstract
{
    
    protected function _construct()
    {        
        $this->_init('chcanpost2module/office', 'id');
    }
    
}