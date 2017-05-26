<?php
/**
 *
 * @package Collinsharper_Canpost
 *
 * @author Shane Harper
 */
class Collinsharper_Canpost_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Shipping
{
    public function getEstimateRates()
    {
        if(!Mage::getStoreConfig('carriers/chcanpost2module/frontend_disable')) {
            return parent::getEstimateRates();
        }

        $ShippingRateGroups = parent::getEstimateRates();
        $newRates = array();
        foreach ($ShippingRateGroups as $code => $_rates) {
            if (Mage::getStoreConfig('carriers/chcanpost2module/frontend_disable') && $code == 'chcanpost2module') {
                continue;
            }
            $newRates[$code] = $_rates;
        }
        return $newRates;
    }
}