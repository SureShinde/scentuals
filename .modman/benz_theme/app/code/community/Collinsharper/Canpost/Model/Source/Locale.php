<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Locale
{
    public function toOptionArray()
    {

        $arr = array();
            $arr[] = array('value'=>'EN', 'label'=> Mage::helper('chcanpost2module')->__('English'));
            $arr[] = array('value'=>'FR', 'label'=> Mage::helper('chcanpost2module')->__('French'));
        return $arr;
    }
}
