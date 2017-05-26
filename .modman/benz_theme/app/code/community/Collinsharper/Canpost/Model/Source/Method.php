<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Method
{
    public function toOptionArray()
    {
        $usps = Mage::getSingleton('chcanpost2module/Carrier_Api');
        $arr = array();
        foreach ($usps->getCode('method') as $v => $l)
        {
            $arr[] = array('value'=>$v, 'label'=>$l);
        }
        return $arr;
    }
}
