<?php

class Collinsharper_Canpost_Model_System_Config_Source_Nondelivery
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'RTS',
                'label' => Mage::helper('chcanpost2module')->__("Return to Sender")
            ),
            array(
                'value' => 'ABAN',
                'label' => Mage::helper('chcanpost2module')->__("Abandon")
            ),
        );
    }

    public function toArray()
    {
        return array(
            'RTS'  => Mage::helper('chcanpost2module')->__("Return to Sender"),
            'ABAN' => Mage::helper('chcanpost2module')->__("Abandon")
        );
    }
}
