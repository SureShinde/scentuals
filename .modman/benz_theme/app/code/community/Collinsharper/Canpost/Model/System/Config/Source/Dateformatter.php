<?php

class Collinsharper_Canpost_Model_System_Config_Source_Dateformatter
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('chcanpost2module')->__("Magento Date-Time formatter")),
            array('value' => 0, 'label' => Mage::helper('chcanpost2module')->__("PHP Date formatter")),
        );
    }

    public function toArray()
    {
        return array(
            1 => Mage::helper('chcanpost2module')->__("Magento Date-Time formatter"),
            0 => Mage::helper('chcanpost2module')->__("PHP Date formatter")
        );
    }
}
