<?php
class ARW_Sebian_Model_Observer
{
	public function configSave()
	{
		$section = Mage::app()->getRequest()->getParam('section');

		if ($section == 'sebian_design' || $section == 'sebian_layout')
		{
            $websiteCode = Mage::app()->getRequest()->getParam('website');
            $storeCode = Mage::app()->getRequest()->getParam('store');

            Mage::getSingleton('sebian/cssgen_generator')->generateCss('design', $websiteCode, $storeCode);
		}
	}
	public function storeEdit(Varien_Event_Observer $observer)
	{
		$store = $observer->getEvent()->getStore();
		$storeCode = $store->getCode();
		$websiteCode = $store->getWebsite()->getCode();
		
		Mage::getSingleton('sebian/cssgen_generator')->generateCss('design', $websiteCode, $storeCode);
	}
}
