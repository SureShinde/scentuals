<?php

?>
<?php
class ARW_Sebian_Model_System_Config_Source_Design_Font_Family_Google_Variants
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'all',             'label' => Mage::helper('adminhtml')->__('All')),
            array('value' => '100italic',       'label' => Mage::helper('adminhtml')->__('Ultra-Light 100 Italic')),
            array('value' => '100',             'label' => Mage::helper('adminhtml')->__('Ultra-Light 100')),
            array('value' => '200italic',       'label' => Mage::helper('adminhtml')->__('Light 200 Italic')),
            array('value' => '200',             'label' => Mage::helper('adminhtml')->__('Light 200')),
            array('value' => '300italic',       'label' => Mage::helper('adminhtml')->__('Book 300 Italic')),
            array('value' => '300',             'label' => Mage::helper('adminhtml')->__('Book 300')),
            array('value' => '400',             'label' => Mage::helper('adminhtml')->__('Normal 400')),
            array('value' => '400italic',       'label' => Mage::helper('adminhtml')->__('Normal 400 Italic')),
            array('value' => '500italic',       'label' => Mage::helper('adminhtml')->__('Medium 500 Italic')),
            array('value' => '500',             'label' => Mage::helper('adminhtml')->__('Medium 500')),
            array('value' => '600',             'label' => Mage::helper('adminhtml')->__('Semi-Bold 600')),
            array('value' => '600italic',       'label' => Mage::helper('adminhtml')->__('Semi-Bold 600 Italic')),
            array('value' => '700',             'label' => Mage::helper('adminhtml')->__('Bold 700')),
            array('value' => '700italic',       'label' => Mage::helper('adminhtml')->__('Bold 700 Italic')),
            array('value' => '800italic',       'label' => Mage::helper('adminhtml')->__('Extra-Bold 800 Italic')),
            array('value' => '800',             'label' => Mage::helper('adminhtml')->__('Extra-Bold 800')),
            array('value' => '900',             'label' => Mage::helper('adminhtml')->__('Ultra-Bold 900')),
            array('value' => '900italic',       'label' => Mage::helper('adminhtml')->__('Ultra-Bold 900 Italic'))
        );
    }

}
