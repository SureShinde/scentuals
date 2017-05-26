<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Shane Harper
 */
class Collinsharper_Canpost_Block_Onepage_Shipping_Method_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available
{
    public function getShippingRates()
    {

        if(!Mage::getStoreConfig('carriers/chcanpost2module/frontend_disable')) {
            return parent::getShippingRates();
        }

        $ShippingRateGroups = parent::getShippingRates();
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