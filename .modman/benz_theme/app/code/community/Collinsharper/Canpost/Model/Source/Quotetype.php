<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Quotetype
{
    public function toOptionArray()
    {

        $arr = array();
        $arr[] = array('value'=>'counter', 'label'=> Mage::helper('chcanpost2module')->__('Counter - will return the regular price paid by retail consumers'));
        $arr[] = array('value'=>'commercial', 'label'=> Mage::helper('chcanpost2module')->__('Commercial - will return the contracted price between Canada Post and the contract holder'));
        return $arr;
    }
}
