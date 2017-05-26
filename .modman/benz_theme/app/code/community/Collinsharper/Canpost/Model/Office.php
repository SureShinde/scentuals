<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Office extends Mage_Core_Model_Abstract
{
    
    
    protected function _construct() {
        
        $this->_init('chcanpost2module/office');
        
    } 
    
    
    public function getByCpOfficeId($office_id)
    {
        
        return $this->getCollection()
             ->addFieldToFilter('cp_office_id', $office_id)
             ->getFirstItem();
        
    }
    
    
    public function getOfficeAddress()
    {
        
        return $this->getAddress().', '.$this->getCity().', '.$this->getProvince().', '.$this->getPostalCode();
        
    }
    
}