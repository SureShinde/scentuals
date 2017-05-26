<?php
class ARW_ProductTab_Model_Product_Productsort 
{
    const NEWFIRST = 1;
    const NEWFIRST_LABEL = 'Newest First';
    const OLDFIRST = 2;
    const OLDFIRST_LABEL = 'Oldest First';
    const RANDOM = 3;
    const RANDOM_LABEL = 'Random';

    public function toOptionArray()
    {
        $helper = Mage::helper('producttab');
        return array(
            array('value' => self::NEWFIRST, 'label' => $helper->__(self::NEWFIRST_LABEL)),
            array('value' => self::OLDFIRST, 'label' => $helper->__(self::OLDFIRST_LABEL)),
            array('value' => self::RANDOM, 'label' => $helper->__(self::RANDOM_LABEL)),

        );
    }
}
