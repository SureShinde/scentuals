<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Model_Source_Output
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'4x6', 'label'=>Mage::helper('chcanpost2module')->__('4 x 6 thermal')),
            array('value'=>'8.5x11', 'label'=>Mage::helper('chcanpost2module')->__('8.5x11 paper')),
            array('value'=>'zpl', 'label'=>Mage::helper('chcanpost2module')->__('4 x 6 thermal ZPL II')),
        );
    }
}
