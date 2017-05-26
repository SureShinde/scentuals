<?php
class ARW_ProductTab_Model_Category_Current_Type 
{
    const RANDOM = 1;
    const RANDOM_LABEL = 'Random Products';
    const BESTSELL = 2;
    const BESTSELL_LABEL = 'Top Sellers';
    const TOPRATED = 3;
    const TOPRATED_LABEL = 'Top Rated';
    const MOSTREVIEWED = 4;
    const MOSTREVIEWED_LABEL = 'Most Reviewed';
    const RECENTLYADDED = 5;
    const RECENTLYADDED_LABEL = 'Recently Added';
	const NEWADD = 6;
    const NEWADD_LABEL = 'New Added';
	const SPECIAL = 7;
    const SPECIAL_LABEL = 'Special';
	const LASTORDERS = 8;
    const LASTORDERS_LABEL = 'Last Orders';
	const DISCOUNT =9;
	const DISCOUNT_LABEL = 'Discount';

    public function toOptionArray()
    {
        $helper = Mage::helper('producttab');
        return array(
            array('value' => self::RANDOM, 'label' => $helper->__(self::RANDOM_LABEL)),
            array('value' => self::BESTSELL, 'label' => $helper->__(self::BESTSELL_LABEL)),
            array('value' => self::TOPRATED, 'label' => $helper->__(self::TOPRATED_LABEL)),
            array('value' => self::MOSTREVIEWED, 'label' => $helper->__(self::MOSTREVIEWED_LABEL)),
            array('value' => self::RECENTLYADDED, 'label' => $helper->__(self::RECENTLYADDED_LABEL)),
			array('value' => self::NEWADD, 'label' => $helper->__(self::NEWADD_LABEL)),
			/* array('value' => self::SPECIAL, 'label' => $helper->__(self::SPECIAL_LABEL)), */
			array('value' => self::LASTORDERS, 'label' => $helper->__(self::LASTORDERS_LABEL)),
			array('value' => self::DISCOUNT, 'label' => $helper->__(self::DISCOUNT_LABEL)),
        );
    }
}
