<?php

/* * ****************************************************
 * Package   : Brand
 * Author    : ArexWorks
 * Copyright : (c) 2015
 * ***************************************************** */
?>
<?php

class ARW_Brand_Model_System_Config_Source_Sortdir
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'asc', 'label' => Mage::helper('adminhtml')->__('Ascending')),
            array('value' => 'desc', 'label' => Mage::helper('adminhtml')->__('Descending'))
        );
    }

}
