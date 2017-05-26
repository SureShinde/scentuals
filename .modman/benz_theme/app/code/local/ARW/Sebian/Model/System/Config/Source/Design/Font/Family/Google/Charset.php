<?php

?>
<?php
class ARW_Sebian_Model_System_Config_Source_Design_Font_Family_Google_Charset
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'all',             'label' => Mage::helper('adminhtml')->__('All')),
            array('value' => 'cyrillic-ext',    'label' => Mage::helper('adminhtml')->__('Cyrillic Extended')),
            array('value' => 'latin',           'label' => Mage::helper('adminhtml')->__('Latin')),
            array('value' => 'greek-ext',       'label' => Mage::helper('adminhtml')->__('Greek Extended')),
            array('value' => 'greek',           'label' => Mage::helper('adminhtml')->__('Greek')),
            array('value' => 'vietnamese',      'label' => Mage::helper('adminhtml')->__('Vietnamese')),
            array('value' => 'latin-ext',       'label' => Mage::helper('adminhtml')->__('Latin Extended')),
            array('value' => 'cyrillic',        'label' => Mage::helper('adminhtml')->__('Cyrillic'))
        );
    }

}
