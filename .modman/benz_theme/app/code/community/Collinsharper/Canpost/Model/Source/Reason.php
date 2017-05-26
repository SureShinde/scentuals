<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Reason
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'GIF', 'label'=>Mage::helper('chcanpost2module')->__('gift')),
            array('value'=>'DOC', 'label'=>Mage::helper('chcanpost2module')->__('document')),
            array('value'=>'SAM', 'label'=>Mage::helper('chcanpost2module')->__('commercial sample')),
            array('value'=>'REP', 'label'=>Mage::helper('chcanpost2module')->__('repair or warranty')),
            array('value'=>'SOG', 'label'=>Mage::helper('chcanpost2module')->__('sale of goods')),
            array('value'=>'OTH', 'label'=>Mage::helper('chcanpost2module')->__('other')),
        );
    }
}