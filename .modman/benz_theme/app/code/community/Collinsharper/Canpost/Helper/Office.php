<?php

/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Office extends Mage_Core_Helper_Abstract {


    public function updateShippingAddress($quote, $deliver_to_post_office, $office_id)
    {

        $is_original_address_saved = Mage::getSingleton('checkout/session')->getOriginalAddressSaved();

        if ($deliver_to_post_office && !empty($office_id)) {

            $cp_office = Mage::getModel('chcanpost2module/office')->load($office_id);

            if ($cp_office->getId()) {

                if (empty($is_original_address_saved)) {

                    Mage::getSingleton('checkout/session')->setOriginalCity($quote->getShippingAddress()->getCity());

                    Mage::getSingleton('checkout/session')->setOriginalAddress($quote->getShippingAddress()->getStreet());

                    Mage::getSingleton('checkout/session')->setOriginalPostalCode($quote->getShippingAddress()->getPostcode());

                    Mage::getSingleton('checkout/session')->setOriginalProvince($quote->getShippingAddress()->getRegion());

                    Mage::getSingleton('checkout/session')->setOfficeId($office_id);

                    Mage::getSingleton('checkout/session')->setOriginalAddressSaved(true);

                }

                $region = Mage::getModel('directory/region')->getCollection()
                                ->addFieldToFilter('code', $cp_office->getProvince())
                                ->addFieldToFilter('country_id', $quote->getShippingAddress()->getCountryId())
                                ->getFirstItem();

                if ($region->getId()) {

                    $region_id = $region->getId();

                } else {

                    $region_id = $cp_office->getProvince();

                }

                $quote->getShippingAddress()
                        ->setCity($cp_office->getCity())
                        ->setStreet($cp_office->getAddress())
                        ->setPostcode($cp_office->getPostalCode())
                        ->setRegionId($region_id)
                        ->save();

            }

        } else if (!empty($is_original_address_saved)){

                $quote->getShippingAddress()
                        ->setCity(Mage::getSingleton('checkout/session')->getOriginalCity())
                        ->setStreet(Mage::getSingleton('checkout/session')->getOriginalAddress())
                        ->setPostcode(Mage::getSingleton('checkout/session')->getOriginalPostalCode())
                        ->setRegion(Mage::getSingleton('checkout/session')->getOriginalProvince())
                        ->save();

                Mage::getSingleton('checkout/session')->setOriginalAddressSaved(false);

        }

    }


    public function getDetails($office_id)
    {

    }


}
