<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Coverage
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'never', 'label'=>Mage::helper('chcanpost2module')->__('Never')),
            array('value'=>'optional', 'label'=>Mage::helper('chcanpost2module')->__('Optional')),
            array('value'=>'always', 'label'=>Mage::helper('chcanpost2module')->__('Always')),
        );
    }
}
